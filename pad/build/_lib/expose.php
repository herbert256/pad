<?php

  // The bodies of a data answer (lib/expose.php): read by build/expose.php once it decided
  // the page answers JSON or CSV, and by exits/exits.php when the output type is one of the
  // two.
  //
  // padExposeData     the exposed variables, name => value, each name checked
  // padExposeJson     the JSON body: one object keyed by the exposed names
  // padExposeList     the list a CSV answer is made of: the first exposed array
  // padExposeCsv      the CSV body: a header row of the columns, then a line per row
  // padExposeCell     a cell a spreadsheet would run as a formula, made text

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

    // A byte that is no UTF-8 - a latin1 database or file - becomes U+FFFD, as the HTML page
    // shows it: partial output made the whole value null, with no error, which a client
    // cannot tell from a real null.

    $json = json_encode ( (object) padExposeData (), JSON_PRETTY_PRINT | JSON_PARTIAL_OUTPUT_ON_ERROR
                                                   | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                                                   | JSON_INVALID_UTF8_SUBSTITUTE );

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

    fputcsv ( $csv, array_map ( 'padExposeCell', $columns ), ',', '"', '' );

    foreach ( $rows as $row ) {

      $line = [];

      foreach ( $columns as $column ) {
        $cell    = $row [$column] ?? '';
        $line [] = padExposeCell ( is_array ( $cell ) || is_object ( $cell ) ? json_encode ( $cell ) : ( is_bool ( $cell ) ? (int) $cell : $cell ) );
      }

      fputcsv ( $csv, $line, ',', '"', '' );

    }

    rewind ( $csv );

    $body = stream_get_contents ( $csv );

    fclose ( $csv );

    return $body;

  }

  // A spreadsheet runs a cell that starts with =, +, - or @ - or with a tab or a carriage
  // return in front of one - as a formula: a visitor's =HYPERLINK(...) or +cmd|... in the
  // data became a link or a command in the spreadsheet of whoever opened the export. Such a
  // text cell gets a ' in front, which a spreadsheet shows as the text it is; a number of
  // either sign is no formula and stays as it is.

  function padExposeCell ( $cell ) {

    if ( is_string ( $cell ) and preg_match ( '/^[=+\-@\t\r]/', $cell ) and ! is_numeric ( $cell ) )
      return "'$cell";

    return $cell;

  }

?>
