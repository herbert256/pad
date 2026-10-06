<?php

  // Data answers: the page that renders orders.pad answering with its data instead, as JSON
  // or as CSV - what a React component, a script or a spreadsheet wants from the same page.
  //
  //   ?orders&padFormat=json             asked for in the URL (padFormat=csv for CSV)
  //   Accept: application/json           asked for by content negotiation (or text/csv)
  //   $padOutputType = 'json'            every page of the application, from its config
  //
  // What may leave the server is an explicit list the page's PHP makes - $padExpose =
  // [ 'orders', 'total' ] in orders.php, or in an _inits.php for a whole directory - and
  // nothing else does: never every variable, never an engine name. A page that names none
  // answers HTML as it always did; asked for data outright, it answers 406. Once the page's
  // PHP has run, build/expose.php decides, and a data answer skips the templates entirely;
  // the writers in exits/output/json.php and csv.php encode the named variables and the web
  // writer sends them with their own content type, ETag and 304, like any page.
  //
  // padClientFormat   what the request asks for: an explicit padFormat as written, or else
  //                   'json' or 'csv' when the Accept header prefers one, '' when not
  // padAcceptFormat   the Accept header read with its quality values - html wins a tie, and
  //                   */* alone asks for nothing, so a browser keeps getting the page
  // padExposeData     the exposed variables, name => value, each name checked
  // padExposeJson     the JSON body: one object keyed by the exposed names
  // padExposeList     the list a CSV answer is made of: the first exposed array
  // padExposeCsv      the CSV body: a header row of the columns, then a line per row
  // padExposeRefuse   the 406 answer for a format this page cannot give

  function padClientFormat () {

    if ( isset ( $_REQUEST ['padFormat'] ) )
      return is_string ( $_REQUEST ['padFormat'] ) ? strtolower ( trim ( $_REQUEST ['padFormat'] ) ) : '?';

    return padAcceptFormat ( $_SERVER ['HTTP_ACCEPT'] ?? '' );

  }

  // Only a type the header names counts for data: application/json and text/csv must be
  // there by name, with a quality above the one html gets - by name, through text/*, or
  // through */*. Without either, the answer is '' and the page renders as before.

  function padAcceptFormat ( $header ) {

    $named = [ 'html' => -1.0, 'json' => -1.0, 'csv' => -1.0 ];
    $text  = -1.0;
    $any   = -1.0;

    $types = [ 'text/html'             => 'html',
               'application/xhtml+xml' => 'html',
               'application/json'      => 'json',
               'text/csv'              => 'csv' ];

    foreach ( explode ( ',', $header ) as $one ) {

      $parts = explode ( ';', $one );
      $name  = strtolower ( trim ( $parts [0] ) );
      $q     = 1.0;

      foreach ( array_slice ( $parts, 1 ) as $parm )
        if ( preg_match ( '/^\s*q\s*=\s*([0-9.]+)\s*$/i', $parm, $match ) )
          $q = (float) $match [1];

      if     ( isset ( $types [$name] ) ) $named [ $types [$name] ] = max ( $named [ $types [$name] ], $q );
      elseif ( $name == 'text/*'        ) $text = max ( $text, $q );
      elseif ( $name == '*/*'           ) $any  = max ( $any,  $q );

    }

    $html = ( $named ['html'] >= 0 ) ? $named ['html'] : ( ( $text >= 0 ) ? $text : max ( $any, 0.0 ) );
    $best = ( $named ['csv'] > $named ['json'] ) ? 'csv' : 'json';

    if ( $named [$best] > 0 and $named [$best] > $html )
      return $best;

    return '';

  }

  // A name that is no variable a page may own - an engine name like padSqlPassword among
  // them - is refused whatever the strict switch says: the list is the one thing standing
  // between the page's variables and the visitor. A name the page never set is the
  // author's slip, named under strict mode and answered as null without it.

  function padExposeData () {

    global $padCheckSyntax, $padExpose;

    $data = [];

    foreach ( (array) $padExpose as $name ) {

      if ( ! is_string ( $name ) or ! padValidVar ( $name ) ) {
        padError ( "\$padExpose names '" . padMakeSafe ( is_string ( $name ) ? $name : gettype ( $name ), 40 ) . "', which is no variable a page may expose" );
        continue;
      }

      if ( ! array_key_exists ( $name, $GLOBALS ) and $padCheckSyntax )
        padError ( "\$padExpose names '$name', which the page never set" );

      $data [$name] = $GLOBALS [$name] ?? NULL;

    }

    return $data;

  }

  function padExposeJson () {

    global $padCheckSyntax, $padExpose;

    if ( ! count ( (array) $padExpose ) and $padCheckSyntax )
      padError ( "a json answer is the variables \$padExpose names, and it names none" );

    $json = json_encode ( (object) padExposeData (), JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR
                                                   | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

    return ( $json === FALSE ) ? '{}' : $json;

  }

  // The first exposed array is the list - total and the other scalars beside it have no
  // place in a table. A record on its own (keys that are names, values that are not
  // arrays) is one row; a list of plain values is one column named after the variable.

  function padExposeList () {

    foreach ( padExposeData () as $name => $value ) {

      if ( ! is_array ( $value ) )
        continue;

      $rows = ( ! array_is_list ( $value ) and ! count ( array_filter ( $value, 'is_array' ) ) ) ? [ $value ] : $value;

      foreach ( $rows as $key => $row )
        if ( ! is_array ( $row ) )
          $rows [$key] = [ $name => $row ];

      return $rows;

    }

    return NULL;

  }

  // The columns are every key the rows use, in the order they first appear, so a row that
  // lacks one gets an empty cell rather than shifting the line. A cell that holds an array
  // is written as its JSON. RFC 4180 quoting, with no escape character of PHP's own.

  function padExposeCsv () {

    $rows = padExposeList ();

    if ( $rows === NULL ) {
      padError ( "a csv answer is the first list \$padExpose names, and none of it is a list" );
      return '';
    }

    $columns = [];

    foreach ( $rows as $row )
      foreach ( array_keys ( $row ) as $column )
        if ( ! in_array ( $column, $columns, TRUE ) )
          $columns [] = $column;

    $csv = fopen ( 'php://temp', 'r+' );

    fputcsv ( $csv, $columns, ',', '"', '' );

    foreach ( $rows as $row ) {

      $line = [];

      foreach ( $columns as $column ) {
        $cell    = $row [$column] ?? '';
        $line [] = is_array ( $cell ) || is_object ( $cell ) ? json_encode ( $cell ) : ( is_bool ( $cell ) ? (int) $cell : $cell );
      }

      fputcsv ( $csv, $line, ',', '"', '' );

    }

    rewind ( $csv );

    $body = stream_get_contents ( $csv );

    fclose ( $csv );

    return $body;

  }

  // Asked for a format the page cannot give: 406, what went wrong told to this machine's
  // own requests only - the name of the page and of the setting are the author's business.

  function padExposeRefuse ( $why ) {

    while ( ob_get_level () )
      ob_end_clean ();

    if ( ! headers_sent () ) {
      http_response_code ( 406 );
      header ( 'Content-Type: text/plain; charset=UTF-8' );
      header ( 'Vary: Accept' );
    }

    echo padLocal () ? $why : 'Not acceptable';

    $stop = 406;
    include PAD . 'exits/exit.php';

  }

?>
