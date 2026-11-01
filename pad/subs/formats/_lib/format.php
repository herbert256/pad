<?php

  // What the formats share (lib/format.php is the entry): the read and the write around a
  // format's own code, its errors, the sniff padContentType asks, and the helpers several
  // formats use - rows and columns, a cell as text, XML text, dates, deterministic ids and
  // the line of a JSON fault.

  // A reader's fault: thrown by padFormatFail with the line it is on - 0 for a format that
  // has no lines (a zip) - and turned into the message padFormatReadGo reports.

  class PadFormatError extends Exception {}

  function padFormatFail ( $line, $why ) {

    throw new PadFormatError ( $why, (int) $line );

  }

  // The line of a byte offset of a text.

  function padFormatLineAt ( $text, $at ) {

    return 1 + substr_count ( substr ( $text, 0, max ( 0, $at ) ), "\n" );

  }

  function padFormatMessage ( $kind, $why, $line ) {

    $name = padFormatDef ( $kind ) ['name'] ?? $kind;

    return $line ? "$name error on line $line: $why" : "$name error: $why";

  }

  // The read: the format found, its text made whole and handed to its reader. A malformed
  // text is an error under the strict check, naming the format and the line, and no rows
  // without it - the lenient walk renders on, as an unknown data type does there.

  function padFormatReadGo ( $kind, $text, $options ) {

    global $padCheckSyntax;

    $found = padFormatFind ( $kind, 'read' );

    if ( $found === '' ) {
      padError ( padFormatUnknown ( $kind, 'read' ) );
      return [];
    }

    if ( is_array ( $text ) )
      return $text;

    $def  = padFormatDef ( $found );
    $text = (string) $text;

    // A text as padData takes one (padDataText) - the indent its lines share and a byte order
    // mark taken off - so a helper's text and a {data} content read alike.

    if ( ! ( $def ['binary'] ?? FALSE ) )
      $text = padDataText ( str_replace ( [ '&open;', '&close;' ], [ '{', '}' ], $text ) );

    padFormatUse ( $found );

    try {
      $rows = $def ['read'] ( $text, $options );
    } catch ( PadFormatError $e ) {
      if ( $padCheckSyntax )
        padError ( padFormatMessage ( $found, $e->getMessage (), $e->getCode () ) );
      return [];
    }

    return is_array ( $rows ) ? $rows : [];

  }

  // The write: the format's writer makes the text, of the value as it is handed over - each
  // writer takes its objects as it must (padFormatRows, padFormatPlain): the JSON answer keeps
  // an object an object, so {} is not written []. A row a writer cannot write - an event
  // without a date - is named under the strict check (padFormatSkip) and left out without it.

  function padFormatWriteGo ( $kind, $data, $options ) {

    $found = padFormatFind ( $kind, 'write' );

    if ( $found === '' ) {
      padError ( padFormatUnknown ( $kind, 'write' ) );
      return '';
    }

    $def = padFormatDef ( $found );

    padFormatUse ( $found );

    $GLOBALS ['padFormatWriting'] = $found;

    try {
      return (string) $def ['write'] ( $data, $options );
    } catch ( PadFormatError $e ) {
      padError ( padFormatMessage ( $found, $e->getMessage (), 0 ) );
      return '';
    } finally {
      $GLOBALS ['padFormatWriting'] = '';
    }

  }

  // A page answer (build/_lib/expose.php padExposeFormat) is the visitor's choice of format:
  // ?orders&padFormat=ics on a page whose rows have no dates is an empty calendar, never an
  // error - $padFormatQuiet says so while it is written.

  function padFormatSkip ( $why ) {

    if ( $GLOBALS ['padCheckSyntax'] and ! ( $GLOBALS ['padFormatQuiet'] ?? FALSE ) )
      padError ( padFormatMessage ( $GLOBALS ['padFormatWriting'] ?? '', $why, 0 ) );

  }

  // The library of a format, and the xref record of its use - the reference application
  // lists the pages that read or write each format.

  function padFormatUse ( $kind ) {

    $def = padFormatDef ( $kind );

    if ( ( $def ['lib'] ?? '' ) !== '' )
      include_once PAD . "subs/formats/_lib/{$def ['lib']}.php";

    if ( ( $GLOBALS ['padInfoXref'] ?? FALSE ) and function_exists ( 'padInfoXref' ) )
      padInfoXref ( 'format', $kind );

  }

  function padFormatUnknown ( $kind, $can ) {

    $name  = padMakeSafe ( is_string ( $kind ) ? $kind : gettype ( $kind ), 30 );
    $exist = padFormatFind ( $kind );
    $list  = implode ( ', ', array_filter ( padFormatKinds (), fn ( $one ) => isset ( padFormatDef ( $one ) [$can] ) ) );

    return $exist ? "the format '$name' " . ( $can == 'read' ? 'writes only - it reads nothing' : 'reads only - it writes nothing' ) . " - these $can: $list"
                  : "there is no format named '$name' - these $can: $list";

  }

  // The format a text is on sight, where padContentType would have taken it for $base: the
  // first format, in file name order, whose sniff says so.

  function padFormatSniffGo ( $text, $base ) {

    foreach ( padFormatKinds () as $kind ) {
      $sniff = padFormatDef ( $kind ) ['sniff'] ?? NULL;
      if ( $sniff and $sniff [0] == $base and $sniff [1] ( $text ) )
        return $kind;
    }

    return '';

  }

  // The sniffs that need more than a pattern - here, as a sniff runs before its format's
  // library is included. NDJSON: two lines or more, each opening with { and closing with } -
  // never one JSON document, whose second line cannot open a value after the first closed
  // one (a brace may still wear the stand-in the tag scanner gave it).

  function padFormatNdjsonSniff ( $text ) {

    $lines = 0;

    foreach ( explode ( "\n", str_replace ( [ '&open;', '&close;' ], [ '{', '}' ], $text ) ) as $line ) {
      $line = trim ( $line );
      if ( $line === '' )
        continue;
      if ( $line [0] != '{' or substr ( $line, -1 ) != '}' )
        return FALSE;
      $lines++;
    }

    return $lines > 1;

  }

  // A Markdown pipe table: a first line that opens with a | and a line of dashes under it.

  function padFormatMarkdownSniff ( $text ) {

    return (bool) preg_match ( '/^\|[^\n]*\|[ \t]*\r?\n[ \t]*\|?[ \t]*:?-+:?[ \t]*(\|[ \t]*:?-+:?[ \t]*)*\|?[ \t]*(\r?\n|$)/', $text );

  }

  // The rows of a table format from any value: a list of records as it is, a record on its
  // own one row, a list of plain values one column named $name - as a CSV answer takes the
  // list it is given (build/_lib/expose.php padExposeList).

  function padFormatRows ( $data, $name = 'value' ) {

    $object = fn ( $one ) => is_object ( $one ) and ! $one instanceof DateTimeInterface and ! $one instanceof Stringable;

    if ( $object ( $data ) )
      $data = padDataFields ( $data );

    if ( ! is_array ( $data ) )
      return ( $data === NULL or $data === '' ) ? [] : [ [ $name => $data ] ];

    $rowish = fn ( $one ) => is_array ( $one ) or $object ( $one );

    if ( ! array_is_list ( $data ) and count ( array_filter ( $data, $rowish ) ) < count ( $data ) )
      $data = [ $data ];

    $rows = [];

    foreach ( $data as $row )
      $rows [] = is_array ( $row ) ? $row : ( $object ( $row ) ? (array) padDataFields ( $row ) : [ $name => $row ] );

    return $rows;

  }

  // Every key the rows use, in the order they first appear.

  function padFormatColumns ( $rows ) {

    $columns = [];

    foreach ( $rows as $row )
      foreach ( array_keys ( $row ) as $column )
        $columns [$column] = TRUE;

    return array_map ( 'strval', array_keys ( $columns ) );

  }

  // A cell as text: a list or a record as its JSON, a boolean 1 or 0 - as a CSV answer
  // writes them - and a float as PHP writes it.

  function padFormatCell ( $value ) {

    if ( is_array ( $value ) or is_object ( $value ) )
      return (string) json_encode ( padJsonFinite ( $value ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE );

    if ( is_bool ( $value ) )
      return $value ? '1' : '0';

    if ( is_float ( $value ) )
      return preg_replace ( '/^(-?\d+)\.0$/D', '$1', padFormatFloat ( $value ) );

    return (string) $value;

  }

  // A spreadsheet runs a cell that starts with =, +, - or @ - or with a tab or a carriage
  // return in front of one - as a formula: a visitor's =HYPERLINK(...) or +cmd|... in the
  // data became a link or a command in the spreadsheet of whoever opened the export. Such a
  // text cell gets a ' in front, which a spreadsheet shows as the text it is; a number of
  // either sign is no formula and stays as it is. The CSV and the TSV writers - a
  // spreadsheet opens a .tsv as it opens a .csv - write their cells and names through it.

  function padFormatCsvCell ( $cell ) {

    if ( is_string ( $cell ) and preg_match ( '/^[=+\-@\t\r]/', $cell ) and ! is_numeric ( $cell ) )
      return "'$cell";

    return $cell;

  }

  // A float as the shortest text that reads back as the same float - as JSON writes one, 0.1
  // as 0.1 and 224617.445991228 whole, where (string) cuts it to 14 digits - with a .0 on a
  // whole number, as a float is told from an integer: 1.0, 5.0E+22.

  function padFormatFloat ( $value ) {

    return var_export ( (float) $value, TRUE );

  }

  // The first field of a row that has a value, of the names given - a feed's title is the
  // row's title, else its name.

  function padFormatPick ( $row, $names ) {

    $value = padFormatPickValue ( $row, $names );

    return ( $value instanceof DateTimeInterface ) ? $value->format ( 'Y-m-d\TH:i:sP' ) : padFormatCell ( $value ?? '' );

  }

  // The same, as the value it is - a date object stays one - or NULL.

  function padFormatPickValue ( $row, $names ) {

    foreach ( $names as $name )
      if ( isset ( $row [$name] ) and ! is_array ( $row [$name] ) ) {
        $value = $row [$name];
        if ( $value instanceof DateTimeInterface or is_int ( $value ) or is_float ( $value ) )
          return $value;
        if ( is_bool ( $value ) )
          return $value ? '1' : '0';
        if ( is_object ( $value ) and ! $value instanceof Stringable )
          continue;
        if ( (string) $value !== '' )
          return (string) $value;
      }

    return NULL;

  }

  // Text for XML: escaped, and the characters XML 1.0 cannot hold - the controls but tab,
  // newline and carriage return, a byte that is no UTF-8 - taken out.

  function padFormatXml ( $text ) {

    $text = mb_scrub ( (string) $text, 'UTF-8' );
    $text = preg_replace ( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x{FFFE}\x{FFFF}]/u', '', $text ) ?? '';

    return htmlspecialchars ( $text, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8' );

  }

  // A date as the formats read one: the ISO forms - 2026-11-01, 2026-11-01T10:30,
  // 2026-11-01 10:30:00Z, 2026-11-01T10:30:00.5+02:00 - and a moment PHP's own parser reads,
  // with its zone when it has one. The answer is [ the moment, whether it has a time, its zone
  // as written ('Z', '+02:00', a zone name, or '' for a local time) ] - or NULL. A date
  // without a time is midnight; a local time is taken in $zone, UTC unless said.

  function padFormatDate ( $value, $zone = '' ) {

    if ( $value instanceof DateTimeInterface )
      return [ DateTimeImmutable::createFromInterface ( $value ), TRUE, $value->format ( 'P' ) ];

    if ( is_int ( $value ) or is_float ( $value ) )
      return [ ( new DateTimeImmutable ( '@' . (int) $value ) ), TRUE, 'Z' ];

    $text = trim ( (string) $value );

    if ( $text === '' )
      return NULL;

    try {
      $tz = new DateTimeZone ( $zone !== '' ? $zone : 'UTC' );
    } catch ( Exception $e ) {
      $tz = new DateTimeZone ( 'UTC' );
    }

    if ( preg_match ( '/^(\d{4})-(\d\d)-(\d\d)$/D', $text, $m ) ) {
      if ( ! checkdate ( (int) $m [2], (int) $m [3], (int) $m [1] ) )
        return NULL;
      return [ new DateTimeImmutable ( $text . ' 00:00:00', $tz ), FALSE, '' ];
    }

    if ( preg_match ( '/^(\d{4})-(\d\d)-(\d\d)[Tt ](\d\d):(\d\d)(?::(\d\d)(\.\d+)?)?\s*(Z|z|[+-]\d\d:?\d\d)?$/D', $text, $m ) ) {
      if ( ! checkdate ( (int) $m [2], (int) $m [3], (int) $m [1] ) or $m [4] > 23 or $m [5] > 59 or ( $m [6] ?? 0 ) > 60 )
        return NULL;
      $written = strtoupper ( $m [8] ?? '' );
      $at      = "{$m[1]}-{$m[2]}-{$m[3]} {$m[4]}:{$m[5]}:" . ( ( $m [6] ?? '' ) !== '' ? $m [6] : '00' );
      $moment  = new DateTimeImmutable ( $at, $written === '' ? $tz : new DateTimeZone ( $written == 'Z' ? 'UTC' : $written ) );
      return [ $moment, TRUE, $written === '' ? ( $zone !== '' ? $zone : '' ) : $written ];
    }

    // Anything else PHP reads as a moment: an RFC 2822 date of a feed, 1 Nov 2026. A number
    // alone is no date - strtotime reads 2026 as a time of today.

    if ( is_numeric ( $text ) or strtotime ( $text ) === FALSE )
      return NULL;

    try {
      $moment = new DateTimeImmutable ( $text, $tz );
    } catch ( Exception $e ) {
      return NULL;
    }

    return [ $moment, TRUE, $moment->format ( 'P' ) ];

  }

  // A UUID made of a text: the same text, the same id - an id that is the same every time
  // the same rows are written. Shaped as a name-based UUID of version 3 (RFC 4122, MD5).

  function padFormatUuid ( $seed ) {

    $hex = md5 ( (string) $seed );

    $hex [12] = '3';
    $hex [16] = dechex ( ( hexdec ( $hex [16] ) & 0x3 ) | 0x8 );

    return substr ( $hex, 0, 8 ) . '-' . substr ( $hex, 8, 4 ) . '-' . substr ( $hex, 12, 4 ) . '-'
         . substr ( $hex, 16, 4 ) . '-' . substr ( $hex, 20, 12 );

  }

  // JSON decoded, or the format's error at the line of the fault: json_decode says what is
  // wrong but not where, so a scan of the text finds the first byte no JSON can have there.

  function padFormatJson ( $text, $lineOffset = 0 ) {

    $value = json_decode ( $text, TRUE, 512, JSON_BIGINT_AS_STRING );

    if ( json_last_error () === JSON_ERROR_NONE )
      return $value;

    padFormatFail ( $lineOffset + padFormatJsonFault ( $text ), json_last_error_msg () );

  }

  function padFormatJsonFault ( $text ) {

    $at = 0;

    padFormatJsonScan ( $text, $at, 0 );

    padFormatJsonSpace ( $text, $at );

    return padFormatLineAt ( $text, $at );

  }

  function padFormatJsonSpace ( $text, &$at ) {

    $at += strspn ( $text, " \t\r\n", $at );

  }

  // One value from $at on, $at left at its end - or at the first byte that does not fit, the
  // scan given up there.

  function padFormatJsonScan ( $text, &$at, $depth ) {

    padFormatJsonSpace ( $text, $at );

    $c = $text [$at] ?? '';

    if ( $depth > 512 )
      return FALSE;

    if ( $c == '{' or $c == '[' ) {

      $close = ( $c == '{' ) ? '}' : ']';
      $at++;
      padFormatJsonSpace ( $text, $at );

      if ( ( $text [$at] ?? '' ) == $close ) {
        $at++;
        return TRUE;
      }

      while ( TRUE ) {

        if ( $c == '{' ) {
          if ( ( $text [$at] ?? '' ) != '"' or ! padFormatJsonScan ( $text, $at, $depth + 1 ) )
            return FALSE;
          padFormatJsonSpace ( $text, $at );
          if ( ( $text [$at] ?? '' ) != ':' )
            return FALSE;
          $at++;
        }

        if ( ! padFormatJsonScan ( $text, $at, $depth + 1 ) )
          return FALSE;

        padFormatJsonSpace ( $text, $at );

        $next = $text [$at] ?? '';

        if ( $next == $close ) {
          $at++;
          return TRUE;
        }

        if ( $next != ',' )
          return FALSE;

        $at++;
        padFormatJsonSpace ( $text, $at );

      }

    }

    if ( $c == '"' ) {
      if ( ! preg_match ( '/\G"(?:[^"\\\\\x00-\x1F]|\\\\(?:["\\\\\/bfnrt]|u[0-9a-fA-F]{4}))*"/', $text, $m, 0, $at ) )
        return FALSE;
      $at += strlen ( $m [0] );
      return TRUE;
    }

    if ( preg_match ( '/\G(?:true|false|null|-?(?:0|[1-9]\d*)(?:\.\d+)?(?:[eE][+-]?\d+)?)/', $text, $m, 0, $at ) ) {
      $at += strlen ( $m [0] );
      return TRUE;
    }

    return FALSE;

  }

  // A value with its objects as the arrays they hold, a date as its ISO text and a float past
  // the range as NULL - for the writers that take any value. padFormatPlainKeep keeps such a
  // float, for a format that writes inf and nan (TOML).

  function padFormatPlain ( $value, $finite = TRUE ) {

    $value = padDataObject ( $value );

    if ( $value instanceof DateTimeInterface )
      return $value->format ( 'Y-m-d\TH:i:sP' );

    if ( is_object ( $value ) )
      return (string) $value;

    if ( $finite and is_float ( $value ) and ! is_finite ( $value ) )
      return NULL;

    if ( is_array ( $value ) )
      foreach ( $value as $key => $one )
        $value [$key] = padFormatPlain ( $one, $finite );

    return $value;

  }

  function padFormatPlainKeep ( $value ) {

    return padFormatPlain ( $value, FALSE );

  }

?>
