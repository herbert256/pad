<?php

  // The bodies of a data answer (lib/expose.php): read by build/expose.php once it decided
  // the page answers data, and by exits/exits.php when the output type is json, csv or
  // format.
  //
  // padExposeData     the exposed variables, name => value, each name checked
  // padExposeJson     the JSON body: one object keyed by the exposed names
  // padExposeList     the list a CSV answer is made of: the first exposed array
  // padExposeCsv      the CSV body: a header row of the columns, then a line per row
  // padExposeFormat   the body in any other format of the formats subsystem
  //
  // The JSON and the CSV are written by the json and csv formats (subs/formats/_lib/data.php),
  // where their writers moved from here unchanged.

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

    // One object, {} when it names nothing - the json format writes it (padFormatJsonWrite).

    return padFormatWrite ( 'json', padExposeData (), [ 'object' => TRUE ] );

  }

  // The first exposed array is the list - total and the other scalars beside it have no
  // place in a table. A record on its own (keys that are names, not every value a row) is
  // one row, a list in it a cell of JSON; a list of plain values is one column named after
  // the variable. A row that is an object - json_decode's - is its properties, as the JSON
  // answer shows it. A record holding a list was taken for rows, a line per value under the
  // header order,0,1, and objects were one column of their JSON.

  function padExposeList () {

    foreach ( padExposeData () as $name => $value ) {

      if ( ! is_array ( $value ) )
        continue;

      $rowish = fn ( $one ) => is_array ( $one ) or is_object ( $one );

      $rows = ( ! array_is_list ( $value ) and count ( array_filter ( $value, $rowish ) ) < count ( $value ) ) ? [ $value ] : $value;

      foreach ( $rows as $key => $row )
        if ( is_object ( $row ) )
          $rows [$key] = get_object_vars ( $row );
        elseif ( ! is_array ( $row ) )
          $rows [$key] = [ $name => $row ];

      return $rows;

    }

    return NULL;

  }

  // The CSV answer: the first list, written by the csv format (padFormatCsvWrite) - a header
  // row of every key the rows use, RFC 4180 quoting, a cell a spreadsheet would run as a
  // formula made text.

  function padExposeCsv () {

    $rows = padExposeList ();

    if ( $rows === NULL ) {
      padError ( "a csv answer is the first list \$padExpose names, and none of it is a list" );
      return '';
    }

    return padFormatWrite ( 'csv', $rows );

  }

  // The answer in any other format of the formats subsystem that writes: a table format - a
  // calendar, a spreadsheet, a feed - the first list, as CSV has it; any other the exposed
  // variables by name, as JSON has them. The page's $padFormatOptions go to the writer - a
  // feed's title= and link=, a calendar's name= - over the defaults a page has for them:
  // its name as the title, its own address as the link and, with the format asked for, as
  // a feed's self.

  function padExposeFormat ( $kind ) {

    global $padFormatOptions, $padGoExt, $padPage, $padPageAsked;

    $def = padFormatDef ( $kind );

    if ( ( $def ['shape'] ?? '' ) == 'rows' ) {
      $data = padExposeList ();
      if ( $data === NULL ) {
        padError ( "a $kind answer is the first list \$padExpose names, and none of it is a list" );
        return '';
      }
    } else
      $data = padExposeData ();

    $page    = (string) ( $padPageAsked ?? $padPage );
    $options = (array) ( $padFormatOptions ?? [] ) + [ 'title' => basename ( $page ),
                                                       'link'  => $padGoExt . $page,
                                                       'self'  => $padGoExt . $page . '&padFormat=' . $kind ];

    // The visitor chose the format, so a row it has no place for - an order in a calendar,
    // without a date - is left out without a word (padFormatSkip).

    $GLOBALS ['padFormatQuiet'] = TRUE;

    try {
      return padFormatWrite ( $kind, $data, $options );
    } finally {
      $GLOBALS ['padFormatQuiet'] = FALSE;
    }

  }

?>
