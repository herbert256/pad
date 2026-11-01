<?php

  // The options of {spreadsheet} that change its cells, each read into the sheet before it is
  // computed - see subs/formulas/spreadsheet.php for the tag itself.
  //
  //   padSheetTarget    what a name of formulas=, totals= and formats= points at: a cell (D2),
  //                     a range (D2:D9), the column whose first row holds the name (total),
  //                     else - written in capitals - a column by its letters (D)
  //   padSheetFormulas  formulas='total=B2*C2, D8=SUM(D2:D7)': a formula a cell, filled over
  //                     a range from its first cell, or down the rows below the first of a
  //                     column - a new column when no first row holds the name
  //   padSheetTotals    totals='total, B': a row below the last with the SUM of each column
  //                     named, Total in the first column when that one has none
  //   padSheetFormats   formats='B:0.00, D:€ #,##0.00, E:0%': a format code a column
  //   padSheetCells     editable='B2:C9, F1': the cells a list of cells and ranges names
  //   padSheetSplitList a list split on the commas outside brackets and "texts"
  //
  // A mistake in one is reported under $padCheckSyntax and the item left out.

  function padSheetSplitList ( $text ) {

    $items  = [];
    $item   = '';
    $depth  = 0;
    $string = FALSE;

    foreach ( mb_str_split ( (string) $text ) as $char ) {
      if ( $string ) {
        if ( $char == '"' ) $string = FALSE;
      }
      elseif ( $char == '"' ) $string = TRUE;
      elseif ( $char == '(' ) $depth++;
      elseif ( $char == ')' ) $depth--;
      elseif ( $char == ',' and $depth <= 0 ) {
        $items [] = trim ( $item );
        $item     = '';
        continue;
      }
      $item .= $char;
    }

    $items [] = trim ( $item );

    return array_values ( array_filter ( $items, fn ( $one ) => $one !== '' ) );

  }

  // [ 'cell', c, r ], [ 'range', c1, r1, c2, r2 ], [ 'column', c, header ] or NULL.

  function padSheetTarget ( $sheet, $name, $new = FALSE ) {

    $name = trim ( $name );

    if ( $at = padFormulaCellAt ( $name ) )
      return [ 'cell', $at [0], $at [1] ];

    if ( preg_match ( '/^([^:]+):([^:]+)$/', $name, $match ) and ( $one = padFormulaCellAt ( $match [1] ) ) and ( $two = padFormulaCellAt ( $match [2] ) ) )
      return [ 'range', min ( $one [0], $two [0] ), min ( $one [1], $two [1] ), max ( $one [0], $two [0] ), max ( $one [1], $two [1] ) ];

    if ( $name === '' )
      return NULL;

    // The column whose first row holds the name - as written, else in any case.

    foreach ( [ TRUE, FALSE ] as $exact )
      foreach ( $sheet ['cells'] [1] ?? [] as $column => $cell )
        if ( is_string ( $cell ['value'] ) and ( $exact ? $cell ['value'] === $name : strcasecmp ( $cell ['value'], $name ) == 0 ) )
          return [ 'column', $column, FALSE ];

    if ( preg_match ( '/^[A-Z]{1,3}$/', $name ) and padFormulaColumn ( $name ) <= padFormulaMaxColumn )
      return [ 'column', padFormulaColumn ( $name ), FALSE ];

    if ( $new and preg_match ( '/^[\p{L}_][\p{L}\p{N}_ .-]*$/u', $name ) )
      return [ 'column', $sheet ['width'] + 1, $name ];

    return NULL;

  }

  function padSheetFormulas ( &$sheet, $text, $what ) {

    global $padCheckSyntax;

    $last = $sheet ['height'];

    foreach ( padSheetSplitList ( $text ) as $item ) {

      $at = strpos ( $item, '=' );

      if ( $at === FALSE or $at == 0 ) {
        if ( $padCheckSyntax )
          padError ( "a formula of $what's formulas= is written target=formula - total=B2*C2, D8=SUM(D2:D7) - not '" . padMakeSafe ( $item, 40 ) . "'" );
        continue;
      }

      $name    = trim ( substr ( $item, 0, $at ) );
      $formula = '=' . ltrim ( substr ( $item, $at + 1 ), '= ' );
      $target  = padSheetTarget ( $sheet, $name, TRUE );

      if ( ! $target ) {
        if ( $padCheckSyntax )
          padError ( "the formulas= of $what puts a formula in '" . padMakeSafe ( $name, 40 ) . "', which is no cell, range or column" );
        continue;
      }

      if ( $target [0] == 'cell' ) {
        padSheetPut ( $sheet, $target [1], $target [2], padSheetTyped ( $formula ) );
        continue;
      }

      if ( $target [0] == 'range' ) {
        [ , $c1, $r1, $c2, $r2 ] = $target;
        for ( $r = $r1; $r <= $r2; $r++ )
          for ( $c = $c1; $c <= $c2; $c++ )
            padSheetPut ( $sheet, $c, $r, padSheetTyped ( padSheetShift ( $formula, $c - $c1, $r - $r1 ) ) );
        continue;
      }

      // A column: the formula is the one of row 2, filled down every row below the first.

      [ , $column, $header ] = $target;

      if ( $header !== FALSE )
        padSheetPut ( $sheet, $column, 1, padSheetTyped ( "'" . $header ) );

      for ( $r = 2; $r <= $last; $r++ )
        padSheetPut ( $sheet, $column, $r, padSheetTyped ( padSheetShift ( $formula, 0, $r - 2 ) ) );

    }

  }

  function padSheetTotals ( &$sheet, $text, $what ) {

    global $padCheckSyntax;

    $last    = $sheet ['height'];
    $row     = $last + 1;
    $columns = [];

    foreach ( padSheetSplitList ( $text ) as $name ) {
      $target = padSheetTarget ( $sheet, $name );
      if ( ! $target or $target [0] != 'column' ) {
        if ( $padCheckSyntax )
          padError ( "the totals= of $what names '" . padMakeSafe ( $name, 40 ) . "', which is no column - a name of the first row, or the letters of one" );
        continue;
      }
      $columns [ $target [1] ] = TRUE;
    }

    if ( ! $columns or $last < 2 )
      return;

    foreach ( array_keys ( $columns ) as $column ) {
      $letters = padFormulaLetters ( $column );
      padSheetPut ( $sheet, $column, $row, padSheetTyped ( "=SUM({$letters}2:$letters$last)" ) );
    }

    if ( ! isset ( $columns [1] ) )
      padSheetPut ( $sheet, 1, $row, padSheetTyped ( "'" . ( padTransCatalog () ['datatable.total'] ?? 'Total' ) ) );

  }

  // The format rules: [ c1, r1, c2, r2, code ] each, the later one deciding where two meet. A
  // rule names a column - B:0.00, total:€ #,##0.00 - a cell - B5:0.0% - or a range -
  // B5:B8:€ #,##0.00. The commas of a code (#,##0) stay in it: an item starts only where a
  // name and its colon follow the comma.

  const padSheetFormatTarget = '(?:\$?[A-Za-z]{1,3}\$?[1-9][0-9]*:\$?[A-Za-z]{1,3}\$?[1-9][0-9]*|\$?[A-Za-z]{1,3}\$?[1-9][0-9]*|[\p{L}_][\p{L}\p{N}_]*)';

  function padSheetFormats ( $sheet, $text, $what ) {

    global $padCheckSyntax;

    $rules = [];
    $items = preg_split ( '/,\s*(?=' . padSheetFormatTarget . '\s*:)/u', trim ( (string) $text ) );

    foreach ( $items as $item ) {

      if ( trim ( $item ) === '' )
        continue;

      if ( ! preg_match ( '/^\s*(' . padSheetFormatTarget . ')\s*:\s?(.*?)\s*$/su', $item, $match ) or $match [2] === '' ) {
        if ( $padCheckSyntax )
          padError ( "a format of $what's formats= is written column:code - B:0.00, total:#,##0.00, B5:B8:0.0% - not '" . padMakeSafe ( $item, 40 ) . "'" );
        continue;
      }

      $target = padSheetTarget ( $sheet, $match [1] );

      if ( ! $target ) {
        if ( $padCheckSyntax )
          padError ( "the formats= of $what names '" . padMakeSafe ( $match [1], 40 ) . "', which is no column, cell or range - a name of the first row, letters, B5 or B5:B8" );
        continue;
      }

      $rules [] = match ( $target [0] ) {
        'cell'   => [ $target [1], $target [2], $target [1], $target [2], $match [2] ],
        'range'  => [ $target [1], $target [2], $target [3], $target [4], $match [2] ],
        'column' => [ $target [1], 1, $target [1], padFormulaMaxRow, $match [2] ]
      };

    }

    return $rules;

  }

  // The code of the last rule that holds a cell, or NULL.

  function padSheetFormatAt ( $rules, $column, $row ) {

    for ( $at = count ( $rules ) - 1; $at >= 0; $at-- )
      if ( $column >= $rules [$at] [0] and $column <= $rules [$at] [2] and $row >= $rules [$at] [1] and $row <= $rules [$at] [3] )
        return $rules [$at] [4];

    return NULL;

  }

  // The name of a sheet that has none: sheet1, sheet2 ... in the order of the page.

  function padSheetNextName () {

    static $count = 0;

    return 'sheet' . ++$count;

  }

  // An option that is on or off: alone or TRUE, 1, on, yes is on; FALSE, 0, off, no is off.

  function padSheetSwitch ( $value, $option, $what ) {

    global $padCheckSyntax;

    if ( is_bool ( $value ) )
      return $value;

    $word = strtolower ( trim ( (string) $value ) );

    if ( in_array ( $word, [ '1', 'on', 'yes', 'true' ], TRUE ) )         return TRUE;
    if ( in_array ( $word, [ '0', 'off', 'no', 'false', '' ], TRUE ) )   return FALSE;

    if ( $padCheckSyntax )
      padError ( "the $option of $what is on or off - $option or $option=FALSE - not '" . padMakeSafe ( $value, 20 ) . "'" );

    return TRUE;

  }

  // Where the sheet stands, for where its edits go: in a {live} region, in a {form}, or in
  // neither - then it is a form of its own.

  function padSheetMode () {

    global $pad, $padTag, $padType, $padFormStack;

    for ( $level = $pad - 1; $level >= 0; $level-- )
      if ( ( $padTag [$level] ?? '' ) === 'live' and ( $padType [$level] ?? '' ) === 'pad' )
        return 'live';

    return $padFormStack ? 'form' : 'own';

  }

  // The texts posted for the cells to edit, typed into them: the change of a live region
  // after the texts of the other cells it carries as its value, else the fields of the form.
  // Only cells to edit, only texts, none longer than 1000 characters.

  function padSheetPosted ( &$sheet, $name, $inputs ) {

    $posted = [];

    if ( padLiveEvent () === "sheet-$name" ) {
      parse_str ( padLiveValue (), $state );
      foreach ( $state as $cell => $text )
        if ( is_string ( $text ) )
          $posted [$cell] = $text;
    }

    $all    = $_POST ['padSheet'] ?? NULL;
    $fields = is_array ( $all ) ? ( $all [$name] ?? NULL ) : NULL;

    if ( is_array ( $fields ) )
      foreach ( $fields as $cell => $text )
        if ( is_string ( $text ) )
          $posted [$cell] = $text;

    foreach ( $posted as $cell => $text ) {

      $at = padFormulaCellAt ( (string) $cell );

      if ( ! $at or ! isset ( $inputs ["{$at [0]}:{$at [1]}"] ) or mb_strlen ( $text ) > 1000 )
        continue;

      $typed = padSheetTyped ( trim ( $text ) );
      $typed ['posted'] = TRUE;

      padSheetPut ( $sheet, $at [0], $at [1], $typed );

    }

  }

  // [ 'c:r' => TRUE ] of the cells a list names.

  function padSheetCells ( $sheet, $text, $what ) {

    global $padCheckSyntax;

    $cells = [];

    foreach ( padSheetSplitList ( $text ) as $item ) {

      $target = padSheetTarget ( $sheet, $item );

      if ( ! $target ) {
        if ( $padCheckSyntax )
          padError ( "the editable= of $what names '" . padMakeSafe ( $item, 40 ) . "', which is no cell, range or column" );
        continue;
      }

      [ $c1, $r1, $c2, $r2 ] = match ( $target [0] ) {
        'cell'   => [ $target [1], $target [2], $target [1], $target [2] ],
        'range'  => array_slice ( $target, 1 ),
        'column' => [ $target [1], 2, $target [1], max ( 2, $sheet ['height'] ) ]
      };

      if ( ( $c2 - $c1 + 1 ) * ( $r2 - $r1 + 1 ) > 10000 ) {
        if ( $padCheckSyntax )
          padError ( "the editable= of $what names more than 10000 cells - '" . padMakeSafe ( $item, 40 ) . "'" );
        continue;
      }

      for ( $r = $r1; $r <= $r2; $r++ )
        for ( $c = $c1; $c <= $c2; $c++ )
          $cells ["$c:$r"] = TRUE;

    }

    return $cells;

  }

?>
