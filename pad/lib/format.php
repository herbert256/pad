<?php

  // Data formats - the formats subsystem in subs/formats/: a format is a file of
  // subs/formats/types/ (padKinds ( 'formats' ), lib/subsystem.php), and the files there are
  // all the formats there are - one is added by adding its file. A format reads text into
  // rows, writes rows as text, or both, and the engine uses it in four places:
  //
  //   {pad:data 'x', format='toml'}...{/pad:data}    the content read as that format (type= says the
  //                                          same); some are known on sight (padFormatSniff)
  //   _data/plan.toml, {local:plan.toml}     a data file read by its extension
  //   ?page&padFormat=ics, Accept: text/calendar
  //                                          a page's $padExpose answered in that format
  //                                          (lib/expose.php, build/expose.php)
  //   padFormatRead / padFormatWrite         the helpers for a page's PHP
  //
  // This file is the entry, loaded on every request; the readers and writers - and
  // subs/formats/_lib/format.php, what they share - are included when a format is used.
  //
  //   padFormatKinds   the formats: the names of the files of subs/formats/types/
  //   padFormatDef     what a format's file says it is - its name, MIME type, extensions,
  //                    shape and its reader and writer
  //   padFormatFind    the format a name or a file extension names, that can read or write
  //   padFormatRead    a text as rows - a strict-check error names the format and the line
  //   padFormatWrite   rows as the text of a format
  //
  // A type file returns an array:
  //
  //   name       the format as people write it - TOML, iCalendar - for its messages
  //   mime       the media type of a page answer, and of the Accept header that asks for it
  //   accept     the media types an Accept header asks for it with, when they are others
  //              than mime - [] for none (INI, sent as text/plain, which asks for no INI)
  //   extension  a list of file extensions, the first the one a download is named with
  //   shape      'rows' - a table, a list of records: what a page answers is the first list
  //              it exposes, as for CSV - or 'data': any nested value, the exposed variables
  //              keyed by their names, as for JSON
  //   lib        the file of subs/formats/_lib/ with its functions, included before it runs
  //   read       fn ( $text, $options ) - the rows, or padFormatFail ( $line, $why )
  //   write      fn ( $data, $options ) - the text
  //   sniff      [ base, fn ( $text ) ] - optional: the text is this format on sight, where
  //              padContentType would have answered base (csv or json) - never where it
  //              answers anything else, so no text read before reads otherwise now
  //   download   'inline' or 'attachment': the Content-Disposition of a page answer, named
  //              after the page; '' or left out sends none (a feed)
  //   binary     TRUE for a format of bytes (xlsx): read from a file as it is, never run as
  //              PAD first or trimmed
  //   raw        TRUE for a format whose white space is data (tsv, where a leading tab is an
  //              empty cell): its text is not trimmed and only an indent of spaces is cut
  //              (padFormatRawText), where any other text is taken as padDataText takes it
  //   options    the options of its reader a tag that reads its file gives -
  //              {local:sales.xlsx sheet='Notes'}

  function padFormatKinds () {

    return padKinds ( 'formats' );

  }

  function padFormatDef ( $kind ) {

    static $defs = [];

    if ( ! array_key_exists ( $kind, $defs ) )
      $defs [$kind] = padKind ( 'formats', $kind ) ? include PAD . "subs/formats/types/$kind.php" : NULL;

    return $defs [$kind];

  }

  // The format a name stands for: a format of that name, else the first one - in file name
  // order - that has it as a file extension (md is markdown, jsonl ndjson, yml yaml). $can
  // is 'read', 'write' or '' for either; a format without that half is no answer.

  function padFormatFind ( $name, $can = '' ) {

    if ( ! is_string ( $name ) or $name === '' )
      return '';

    $name = strtolower ( $name );

    if ( padKind ( 'formats', $name ) and ( $can === '' or isset ( padFormatDef ( $name ) [$can] ) ) )
      return $name;

    foreach ( padFormatKinds () as $kind ) {
      $def = padFormatDef ( $kind );
      if ( in_array ( $name, (array) ( $def ['extension'] ?? [] ), TRUE ) and ( $can === '' or isset ( $def [$can] ) ) )
        return $kind;
    }

    return '';

  }

  // The format that reads a fetched body of a media type (lib/curl.php) - the format whose
  // accept, or else mime, names it: application/x-ndjson is NDJSON, text/calendar iCalendar,
  // application/toml TOML - or '' for none, the content type then read as before (html, xml,
  // json ...). A format of bytes is none: a fetched body is text, trimmed. The types of
  // every page and API are answered without a format looked at.

  function padFormatMedia ( $type ) {

    $type = strtolower ( trim ( explode ( ';', (string) $type ) [0] ) );

    if ( $type === '' or in_array ( $type, [ 'text/html', 'application/json', 'text/plain', 'application/xml', 'text/xml', 'text/csv' ], TRUE ) )
      return '';

    foreach ( padFormatKinds () as $kind ) {
      $def = padFormatDef ( $kind );
      if ( isset ( $def ['read'] ) and ! ( $def ['binary'] ?? FALSE ) and in_array ( $type, $def ['accept'] ?? [ $def ['mime'] ?? '' ], TRUE ) )
        return $kind;
    }

    return '';

  }

  // A text as rows. $kind is a format or one of its extensions; a format that does not read,
  // or none of the name, is an error under every check - the author named it. The input is
  // a template's text as {pad:data} hands it over - a brace still wearing the stand-in the tag
  // scanner gave it (&open; &close;) is a brace again - or the bytes of a binary format.

  function padFormatRead ( $kind, $text, $options = [] ) {

    include_once PAD . 'subs/formats/_lib/format.php';

    return padFormatReadGo ( $kind, $text, (array) $options );

  }

  // The format a text is on sight, asked by padContentType where it would have answered $base
  // - csv, its last resort, or json for a text opening with a brace - or ''. Only a text of
  // more than one line is looked at: no format is known by one line, and a single word - a
  // data name - is asked about all the time.

  function padFormatSniff ( $text, $base ) {

    if ( ! str_contains ( $text, "\n" ) )
      return '';

    include_once PAD . 'subs/formats/_lib/format.php';

    return padFormatSniffGo ( $text, $base );

  }

  // Rows - or any value, for a format of the data shape - as the text of a format. Options
  // are the format's own: title= and link= of a feed, name= of a calendar, sheet= of a
  // spreadsheet ...

  function padFormatWrite ( $kind, $data, $options = [] ) {

    include_once PAD . 'subs/formats/_lib/format.php';

    return padFormatWriteGo ( $kind, $data, (array) $options );

  }

?>
