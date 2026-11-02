<?php

  // Decides whether this request is answered with the page's data instead of its template
  // (lib/expose.php). Runs from build/build.php once the PHP chain - _inits.php, the page's
  // own .php, _exits.php - has run, because that is where $padExpose is set; for the
  // request's own page only, which inits/app.php marks - a {pad:page} rendered inside it never
  // turns the response into data.
  //
  // The json and csv output types answer data for every page, in the one of the two the
  // request asks for, if it does - and so does the format type, in its $padOutputFormat or
  // the format the request asks for. Otherwise a request asking for data gets it when the
  // page exposes something; asked outright with ?padFormat=, a page that exposes nothing,
  // or a format that does not exist or does not write, answers 406, while a request that
  // only said so in its Accept header gets the HTML page as always.
  //
  // JSON and CSV keep their own output types; any other format of the formats subsystem
  // (lib/format.php) that writes - ics, xlsx, toml, rss ... - answers through the output type
  // 'format', its format in $padOutputFormat (padExposeKind, lib/expose.php).
  //
  // A data answer switches the output type for the rest of the request and empties the
  // source about to be rendered - _lib snippets, wrappers and page alike - so no template
  // runs; the exit writer then encodes what the page exposes.

  if ( ! ( $padExposeCheck ?? FALSE ) )
    return;

  $padExposeCheck  = FALSE;
  $padExposeFormat = '';
  $padExposeKind   = padExposeKind ( $padClientFormat );
  $padExposeClient = ( $padExposeKind !== '' );

  if ( $padOutputType == 'format' and padFormatFind ( $padOutputFormat, 'write' ) === '' ) {
    padError ( "the output type format writes \$padOutputFormat, and '" . padMakeSafe ( (string) $padOutputFormat, 20 ) . "' is no format that writes" );
    $padOutputType = 'web';
    include PAD . 'config/output/web.php';
  }

  if ( $padOutputType == 'json' or $padOutputType == 'csv' or $padOutputType == 'format' )

    $padExposeFormat = $padExposeClient ? $padExposeKind : ( $padOutputType == 'format' ? padFormatFind ( $padOutputFormat, 'write' ) : $padOutputType );

  elseif ( $padExposeClient ) {

    if ( count ( (array) $padExpose ) )
      $padExposeFormat = $padExposeKind;
    elseif ( $padClientFormatAsked )
      padExposeRefuse ( "page '$padPage' answers no $padExposeKind - its .php names nothing in \$padExpose" );

  } elseif ( $padClientFormatAsked and $padClientFormat !== 'html' and $padClientFormat !== '' )

    padExposeRefuse ( ( padFormatFind ( $padClientFormat, 'read' ) !== ''
                        ? "the format '" . padMakeSafe ( $padClientFormat, 20 ) . "' is read only, no page answers in it"
                        : "there is no format named '" . padMakeSafe ( $padClientFormat, 20 ) . "'" )
                      . ' - html or ' . implode ( ', ', array_filter ( padFormatKinds (), fn ( $kind ) => padExposeKind ( $kind ) !== '' ) ) );

  if ( ! $padExposeFormat )
    return;

  include_once PAD . 'build/_lib/expose.php';

  // A table - CSV, a calendar, a spreadsheet, a feed - is made of a list: a request for one
  // where the page has no list is the visitor's to hear about when asked with padFormat=,
  // where the type set in the configuration is the author's (exits/output/csv.php). Asked
  // only through Accept, the page answers as it would without the header - its HTML, or the
  // output type of its configuration - as a header that names a format never ends in 406.

  if ( ( padFormatDef ( $padExposeFormat ) ['shape'] ?? '' ) == 'rows' and $padExposeClient and padExposeList () === NULL ) {

    if ( $padClientFormatAsked )
      padExposeRefuse ( "page '$padPage' answers no $padExposeFormat - nothing it names in \$padExpose is a list" );

    $padExposeFormat = match ( $padOutputType ) { 'json', 'csv' => $padOutputType, 'format' => padFormatFind ( $padOutputFormat, 'write' ), default => '' };

    if ( ! $padExposeFormat )
      return;

  }

  // The type's settings are loaded unless the configuration pass already applied them -
  // that pass, which the application's config may have refined, stands. The format type's
  // are loaded always: its content type is the format's, which the request may have chosen.

  if ( $padExposeFormat == 'json' or $padExposeFormat == 'csv' )
    $padOutputType = $padExposeFormat;
  else {
    $padOutputType   = 'format';
    $padOutputFormat = $padExposeFormat;
  }

  if ( $padOutputType != ( $padConfigSet ['outputType'] ?? $padConfigDefault ['outputType'] ) or $padOutputType == 'format' )
    include PAD . "config/output/$padOutputType.php";

  $padBuildLib  = '';
  $padBuildBase = '@page@';
  $padBuildPage = '';

?>
