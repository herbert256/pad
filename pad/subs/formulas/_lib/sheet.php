<?php

  // A sheet of cells and its computation - what {spreadsheet}, padSheet and padFormula share.
  //
  // A sheet is [ 'cells' => [ row => [ column => cell ] ], 'width', 'height' ], rows and
  // columns counted from 1, each cell [ 'source' => the text as typed, 'value' => a constant,
  // 'formula' => '=...' or NULL, 'format' => the format its typing gave it or NULL ].
  //
  //   padSheetTyped     a text as a cell takes it when typed: =... a formula, 42 and 1,250.5
  //                     numbers, 12% a number shown as a percentage, 2024-01-15 a date and
  //                     13:45 a time, $4.50 an amount, TRUE and FALSE, '... a text whatever
  //                     follows the apostrophe, anything else a text
  //   padSheetRead      the text of a {spreadsheet} pair as rows of cell texts: a line a row,
  //                     a comma between cells - a tab when a line holds one, as a range copied
  //                     from a spreadsheet is - a cell in "quotes" where it holds a comma, a
  //                     formula running to the comma outside its brackets and texts, so
  //                     =SUM(B2,C2) needs no quotes
  //   padSheetFromData  a sheet from rows: records (a row of named fields each) under a first
  //                     row of the field names, a list of rows of values, or cells by their
  //                     names - [ 'A1' => 10, 'B1' => '=A1*2' ]; a value of a record is never a
  //                     formula - a record is data, and =... in it is text
  //   padSheetShift     a formula moved by columns and rows, its relative references with it -
  //                     =B2*C2 filled down a row is =B3*C3, $B$2 stays
  //   padSheetCompute   every formula's value, in the order they need one another: a formula
  //                     that cannot be read is #ERROR!, one in a circle of references #REF!,
  //                     the cells of the circle named in its title
  //   padSheetShow      a value as its cell shows it, through a format code or General

  function padSheetNew () {

    return [ 'cells' => [], 'width' => 0, 'height' => 0 ];

  }

  function padSheetPut ( &$sheet, $column, $row, $cell ) {

    $sheet ['cells'] [$row] [$column] = $cell;
    $sheet ['width']  = max ( $sheet ['width'],  $column );
    $sheet ['height'] = max ( $sheet ['height'], $row );

  }

  function padSheetGet ( $sheet, $column, $row ) {

    return $sheet ['cells'] [$row] [$column] ?? NULL;

  }

  // A text as a typed cell. $formulas: whether =... is a formula and '... a text without its
  // apostrophe - a record's value is data, and stays the text it is: no formula, no
  // apostrophe taken off, only its numbers, dates and logical values read as such.

  function padSheetTyped ( $text, $formulas = TRUE ) {

    $text = (string) $text;
    $cell = [ 'source' => $text, 'value' => NULL, 'formula' => NULL, 'format' => NULL ];
    $trim = trim ( $text );

    if ( $trim === '' )
      return $cell;

    if ( $formulas and $trim [0] == "'" ) {
      $cell ['value'] = substr ( $trim, 1 );
      return $cell;
    }

    if ( $formulas and $trim [0] == '=' and strlen ( $trim ) > 1 ) {
      $cell ['formula'] = $trim;
      return $cell;
    }

    $word = strtoupper ( $trim );

    if ( $word === 'TRUE' or $word === 'FALSE' ) {
      $cell ['value'] = ( $word === 'TRUE' );
      return $cell;
    }

    if ( $formulas and in_array ( $word, padFormulaErrorCodes, TRUE ) ) {
      $cell ['value'] = padFormulaError ( $word );
      return $cell;
    }

    // A number past what a float holds - 1e400, or 400 digits - stays the text it is, as
    // Excel keeps it; one below 2.2251E-308 is 0 (padFormulaKept).

    if ( preg_match ( '/^[+-]?(?:\d{1,3}(?:,\d{3})+|\d+)?(?:\.\d+)?(?:[eE][+-]?\d+)?$/', $trim ) and preg_match ( '/\d/', $trim )
         and ( $number = padFormulaKept ( (float) str_replace ( ',', '', $trim ) ) ) !== NULL ) {
      $cell ['value'] = $number;
      return $cell;
    }

    if ( preg_match ( '/^[+-]?(?:\d+)?(?:\.(\d+))?%$/', $trim, $match ) and preg_match ( '/\d/', $trim )
         and ( $number = padFormulaKept ( (float) rtrim ( $trim, '%' ) / 100 ) ) !== NULL ) {
      $cell ['value']  = $number;
      $cell ['format'] = isset ( $match [1] ) ? '0.' . str_repeat ( '0', strlen ( $match [1] ) ) . '%' : '0%';
      return $cell;
    }

    if ( preg_match ( '/^([+-]?)([' . padFormulaCurrencies . '])\s*((?:\d{1,3}(?:,\d{3})+|\d+)(?:\.(\d+))?)$/u', $trim, $match )
         and ( $number = padFormulaKept ( ( $match [1] == '-' ? -1 : 1 ) * (float) str_replace ( ',', '', $match [3] ) ) ) !== NULL ) {
      $cell ['value']  = $number;
      $cell ['format'] = $match [2] . ( str_contains ( $trim, $match [2] . ' ' ) ? ' ' : '' ) . '#,##0' . ( isset ( $match [4] ) ? '.00' : '' );
      return $cell;
    }

    if ( preg_match ( '/^\d{4}-\d{1,2}-\d{1,2}(?:[ T]\d{1,2}:\d{2}(:\d{2})?)?$/', $trim, $match ) or preg_match ( '/^\d{1,2}:\d{2}(:\d{2})?$/', $trim, $match ) ) {
      $date = padFormulaParseDate ( $trim );
      if ( $date !== NULL ) {
        $cell ['value']  = (float) $date;
        $clock           = isset ( $match [1] ) ? 'hh:mm:ss' : 'hh:mm';
        $cell ['format'] = str_contains ( $trim, '-' ) ? ( str_contains ( $trim, ':' ) ? "yyyy-mm-dd $clock" : 'yyyy-mm-dd' ) : $clock;
        return $cell;
      }
    }

    $cell ['value'] = $text;

    return $cell;

  }

  // A value of the page's own - from data= or a post - as a cell.

  function padSheetValue ( $value, $formulas ) {

    if ( $value === NULL )
      return padSheetTyped ( '' );

    if ( is_bool ( $value ) )
      return [ 'source' => $value ? 'TRUE' : 'FALSE', 'value' => $value, 'formula' => NULL, 'format' => NULL ];

    // A float of the page's that is no number a cell holds - INF, NAN - is #NUM!.

    if ( is_int ( $value ) or is_float ( $value ) ) {
      $number = padFormulaKept ( (float) $value );
      return [ 'source' => padFormulaNumberText ( (float) $value ), 'value' => $number ?? padFormulaError ( '#NUM!' ), 'formula' => NULL, 'format' => NULL ];
    }

    if ( ! is_scalar ( $value ) and ! $value instanceof Stringable )
      return padSheetTyped ( '' );

    return padSheetTyped ( (string) $value, $formulas );

  }

  function padSheetRead ( $text ) {

    $lines = explode ( "\n", padChartDedent ( (string) $text ) );
    $tab   = FALSE;

    foreach ( $lines as $line )
      if ( str_contains ( trim ( $line, " \r" ), "\t" ) )
        $tab = TRUE;

    $rows = [];

    foreach ( $lines as $line )
      $rows [] = trim ( $line, " \t\r" ) === '' ? [] : padSheetSplit ( rtrim ( $line, "\r" ), $tab ? "\t" : ',' );

    while ( $rows and end ( $rows ) === [] )
      array_pop ( $rows );

    return $rows;

  }

  function padSheetSplit ( $line, $separator ) {

    $cells  = [];
    $length = strlen ( $line );
    $at     = 0;

    while ( TRUE ) {

      while ( $at < $length and $line [$at] == ' ' )
        $at++;

      if ( $at < $length and $line [$at] == '"' ) {

        $value = '';
        $at++;

        while ( $at < $length ) {
          if ( $line [$at] == '"' ) {
            if ( ( $line [$at + 1] ?? '' ) == '"' ) {
              $value .= '"';
              $at += 2;
              continue;
            }
            $at++;
            break;
          }
          $value .= $line [$at++];
        }

        $end   = strpos ( $line, $separator, $at );
        $end   = $end === FALSE ? $length : $end;
        $value .= rtrim ( substr ( $line, $at, $end - $at ) );
        $at    = $end;

      } elseif ( $at < $length and $line [$at] == '=' ) {

        $start  = $at;
        $depth  = 0;
        $string = FALSE;

        for ( ; $at < $length; $at++ ) {
          $char = $line [$at];
          if     ( $string )                            { if ( $char == '"' ) $string = FALSE; }
          elseif ( $char == '"' )                       $string = TRUE;
          elseif ( $char == '(' )                       $depth++;
          elseif ( $char == ')' )                       $depth--;
          elseif ( $char == $separator and $depth <= 0 ) break;
        }

        $value = rtrim ( substr ( $line, $start, $at - $start ) );

      } else {

        $end   = strpos ( $line, $separator, $at );
        $end   = $end === FALSE ? $length : $end;
        $value = rtrim ( substr ( $line, $at, $end - $at ) );
        $at    = $end;

      }

      $cells [] = $value;

      if ( $at >= $length )
        break;

      $at++;

      if ( $at == $length ) {
        $cells [] = '';
        break;
      }

    }

    return $cells;

  }

  function padSheetFromRows ( $rows ) {

    $sheet = padSheetNew ();

    foreach ( $rows as $index => $row )
      foreach ( $row as $column => $text )
        if ( $text !== '' )
          padSheetPut ( $sheet, $column + 1, $index + 1, padSheetTyped ( $text ) );

    $sheet ['height'] = max ( $sheet ['height'], count ( $rows ) );

    return $sheet;

  }

  function padSheetFromData ( $data ) {

    $sheet = padSheetNew ();
    $data  = is_array ( $data ) ? $data : [];

    if ( ! $data )
      return $sheet;

    // Cells by name.

    $named = TRUE;

    foreach ( array_keys ( $data ) as $key )
      if ( ! is_string ( $key ) or ! padFormulaCellAt ( $key ) ) {
        $named = FALSE;
        break;
      }

    if ( $named ) {
      foreach ( $data as $key => $value ) {
        [ $column, $row ] = padFormulaCellAt ( $key );
        padSheetPut ( $sheet, $column, $row, padSheetValue ( $value, TRUE ) );
      }
      return $sheet;
    }

    // A list of rows of values, or records under their field names.

    $lists = TRUE;

    foreach ( $data as $row )
      if ( is_array ( $row ) and ! array_is_list ( $row ) ) {
        $lists = FALSE;
        break;
      }

    if ( $lists ) {
      $index = 0;
      foreach ( $data as $row ) {
        $index++;
        foreach ( ( is_array ( $row ) ? $row : [ $row ] ) as $column => $value )
          if ( ! is_array ( $value ) )
            padSheetPut ( $sheet, $column + 1, $index, padSheetValue ( $value, TRUE ) );
      }
      $sheet ['height'] = max ( $sheet ['height'], $index );
      return $sheet;
    }

    $fields = [];

    foreach ( $data as $row )
      foreach ( ( is_array ( $row ) ? $row : [] ) as $field => $value )
        if ( ! is_array ( $value ) and ! is_object ( $value ) or $value instanceof Stringable )
          $fields [ (string) $field ] = TRUE;

    $fields = array_keys ( $fields );

    foreach ( $fields as $column => $field )
      padSheetPut ( $sheet, $column + 1, 1, padSheetValue ( (string) $field, FALSE ) );

    $index = 1;

    foreach ( $data as $row ) {
      $index++;
      foreach ( $fields as $column => $field )
        if ( is_array ( $row ) and array_key_exists ( $field, $row ) )
          padSheetPut ( $sheet, $column + 1, $index, padSheetValue ( $row [$field], FALSE ) );
    }

    $sheet ['height'] = max ( $sheet ['height'], $index );

    return $sheet;

  }

  // A formula moved: each reference that is not fixed by a $ moves by the columns and rows
  // given; one that would leave the sheet - above row 1, left of column A - is #REF!. A
  // formula that cannot be read stays as it is: its cell reports it.

  function padSheetShift ( $formula, $columns, $rows ) {

    if ( $columns == 0 and $rows == 0 )
      return $formula;

    try {
      $tokens = padFormulaTokens ( $formula );
    } catch ( Exception $exception ) {
      return $formula;
    }

    foreach ( array_reverse ( $tokens ) as $token ) {

      if ( $token [0] != 'ref' )
        continue;

      $text = padSheetShiftRef ( $token [3], $token [4], $columns, $rows );

      $formula = substr_replace ( $formula, $text, $token [2], strlen ( $token [3] ) );

    }

    return $formula;

  }

  function padSheetShiftRef ( $text, $form, $columns, $rows ) {

    $bad = FALSE;

    $column = function ( $fixed, $letters ) use ( $columns, &$bad ) {
      if ( $fixed !== '' )
        return $fixed . strtoupper ( $letters );
      $number = padFormulaColumn ( $letters ) + $columns;
      if ( $number < 1 or $number > padFormulaMaxColumn )
        $bad = TRUE;
      return padFormulaLetters ( max ( 1, $number ) );
    };

    $row = function ( $fixed, $digits ) use ( $rows, &$bad ) {
      if ( $fixed !== '' )
        return $fixed . $digits;
      $number = (int) $digits + $rows;
      if ( $number < 1 or $number > padFormulaMaxRow )
        $bad = TRUE;
      return (string) $number;
    };

    if ( $form == 'cells' ) {
      preg_match ( '/^(\$?)([A-Za-z]{1,3})(\$?)([0-9]+)(?::(\$?)([A-Za-z]{1,3})(\$?)([0-9]+))?$/', $text, $m );
      $out = $column ( $m [1], $m [2] ) . $row ( $m [3], $m [4] );
      if ( isset ( $m [6] ) )
        $out .= ':' . $column ( $m [5], $m [6] ) . $row ( $m [7], $m [8] );
    } elseif ( $form == 'columns' ) {
      preg_match ( '/^(\$?)([A-Za-z]{1,3}):(\$?)([A-Za-z]{1,3})$/', $text, $m );
      $out = $column ( $m [1], $m [2] ) . ':' . $column ( $m [3], $m [4] );
    } else {
      preg_match ( '/^(\$?)([0-9]+):(\$?)([0-9]+)$/', $text, $m );
      $out = $row ( $m [1], $m [2] ) . ':' . $row ( $m [3], $m [4] );
    }

    return $bad ? '#REF!' : $out;

  }

  // Every formula of the sheet computed. Each formula cell gets 'result', and, when it has
  // one, 'problem' - [ message, position ] of a formula that cannot be read, or that calls a
  // function that is not there, or with the wrong number of arguments - and 'circle', the
  // cells of a circle of references it stands in. $seed seeds RAND.

  function padSheetCompute ( &$sheet, $seed = 0 ) {

    $formulas = [];
    $trees    = [];

    foreach ( $sheet ['cells'] as $row => $cells )
      foreach ( $cells as $column => $cell )
        if ( $cell ['formula'] !== NULL ) {

          $key = "$column:$row";
          $formulas [$key] = [ $column, $row ];

          try {
            $trees [$key] = padFormulaParse ( $cell ['formula'], TRUE );
          } catch ( Exception $exception ) {
            $sheet ['cells'] [$row] [$column] ['problem'] = [ $exception->getMessage (), $exception->getCode () ];
            try {
              $trees [$key] = padFormulaParse ( $cell ['formula'], FALSE );
            } catch ( Exception $again ) {
              $trees [$key] = [ 'v', padFormulaError ( '#ERROR!' ) ];
            }
          }

        }

    // Which formulas each one needs: those in the cells its references reach.

    $needs = [];

    foreach ( $trees as $key => $tree ) {

      $refs = [];
      padFormulaRefs ( $tree, $refs );
      $needs [$key] = [];

      foreach ( $refs as [ $c1, $r1, $c2, $r2 ] ) {

        $c2 = min ( $c2, $sheet ['width'] );
        $r2 = min ( $r2, $sheet ['height'] );

        if ( $c1 > $c2 or $r1 > $r2 )
          continue;

        if ( ( $c2 - $c1 + 1 ) * ( $r2 - $r1 + 1 ) <= count ( $formulas ) ) {
          for ( $r = $r1; $r <= $r2; $r++ )
            for ( $c = $c1; $c <= $c2; $c++ )
              if ( isset ( $formulas ["$c:$r"] ) )
                $needs [$key] ["$c:$r"] = TRUE;
        } else
          foreach ( $formulas as $other => [ $c, $r ] )
            if ( $c >= $c1 and $c <= $c2 and $r >= $r1 and $r <= $r2 )
              $needs [$key] [$other] = TRUE;

      }

      $needs [$key] = array_keys ( $needs [$key] );

    }

    // The formulas in the order they need one another, a circle of them together.

    $results = [];

    $env = [
      'width'  => $sheet ['width'],
      'height' => $sheet ['height'],
      'seed'   => $seed,
      'value'  => function ( $column, $row ) use ( &$sheet, &$results ) {
        $key = "$column:$row";
        if ( array_key_exists ( $key, $results ) )
          return $results [$key];
        return $sheet ['cells'] [$row] [$column] ['value'] ?? NULL;
      }
    ];

    foreach ( padSheetOrder ( array_keys ( $formulas ), $needs ) as $group ) {

      $circle = count ( $group ) > 1 || in_array ( $group [0], $needs [ $group [0] ], TRUE );

      if ( $circle ) {
        $path = padSheetCircle ( $group, $needs );
        foreach ( $group as $key ) {
          [ $column, $row ] = $formulas [$key];
          $results [$key] = padFormulaError ( '#REF!' );
          $sheet ['cells'] [$row] [$column] ['circle'] = $path;
        }
        continue;
      }

      $key = $group [0];
      [ $column, $row ] = $formulas [$key];

      $env ['at']   = [ $column, $row ];
      $env ['rand'] = 0;

      $value = padFormulaScalar ( padFormulaEval ( $trees [$key], $env ), $env );

      $results [$key] = $value === NULL ? 0.0 : $value;

      $sheet ['cells'] [$row] [$column] ['format'] = padSheetFormatOf ( $sheet, $trees [$key], 0 );

    }

    foreach ( $formulas as $key => [ $column, $row ] ) {
      $sheet ['cells'] [$row] [$column] ['result'] = $results [$key];
      $sheet ['cells'] [$row] [$column] ['tree']   = $trees [$key];
    }

  }

  // The strongly connected groups of the formulas, each after the groups it needs - Tarjan's
  // walk, kept on a stack of its own rather than PHP's: a column of a thousand formulas each
  // needing the one above would nest a thousand calls deep.

  function padSheetOrder ( $keys, $needs ) {

    $index  = [];
    $low    = [];
    $on     = [];
    $stack  = [];
    $groups = [];
    $count  = 0;

    foreach ( $keys as $start ) {

      if ( isset ( $index [$start] ) )
        continue;

      $work = [ [ $start, 0 ] ];

      while ( $work ) {

        $top = count ( $work ) - 1;
        [ $node, $edge ] = $work [$top];

        if ( $edge == 0 and ! isset ( $index [$node] ) ) {
          $index [$node] = $low [$node] = $count++;
          $stack [] = $node;
          $on [$node] = TRUE;
        }

        if ( $edge < count ( $needs [$node] ) ) {
          $work [$top] [1]++;
          $next = $needs [$node] [$edge];
          if ( ! isset ( $index [$next] ) )
            $work [] = [ $next, 0 ];
          elseif ( isset ( $on [$next] ) )
            $low [$node] = min ( $low [$node], $index [$next] );
          continue;
        }

        array_pop ( $work );

        if ( $work ) {
          $parent = $work [ count ( $work ) - 1 ] [0];
          $low [$parent] = min ( $low [$parent], $low [$node] );
        }

        if ( $low [$node] == $index [$node] ) {
          $group = [];
          do {
            $member = array_pop ( $stack );
            unset ( $on [$member] );
            $group [] = $member;
          } while ( $member !== $node );
          $groups [] = $group;
        }

      }

    }

    return $groups;

  }

  // A circle through a group, from its first cell in the order a sheet is read - row by row -
  // the shortest way back to it: A1 → B1 → A1.

  function padSheetCircle ( $group, $needs ) {

    usort ( $group, function ( $a, $b ) {
      [ $ca, $ra ] = explode ( ':', $a );
      [ $cb, $rb ] = explode ( ':', $b );
      return [ (int) $ra, (int) $ca ] <=> [ (int) $rb, (int) $cb ];
    } );

    $start  = $group [0];
    $inside = array_flip ( $group );
    $from   = [];
    $queue  = [ $start ];
    $found  = FALSE;

    while ( $queue and ! $found ) {
      $node = array_shift ( $queue );
      foreach ( $needs [$node] as $next ) {
        if ( ! isset ( $inside [$next] ) )
          continue;
        if ( $next === $start ) {
          $from [$start] = $node;
          $found = TRUE;
          break;
        }
        if ( ! isset ( $from [$next] ) ) {
          $from [$next] = $node;
          $queue [] = $next;
        }
      }
    }

    $path = [ $start ];
    $node = $from [$start] ?? $start;

    while ( $node !== $start and count ( $path ) <= count ( $group ) ) {
      array_unshift ( $path, $node );
      $node = $from [$node];
    }

    array_unshift ( $path, $start );

    $names = array_map ( function ( $key ) {
      [ $column, $row ] = explode ( ':', $key );
      return padFormulaCellName ( (int) $column, (int) $row );
    }, $path );

    return implode ( ' → ', $names );

  }

  // The format a formula's cell takes when its column has none: DATE, TODAY, EDATE and
  // EOMONTH answer a date, NOW a date and a time; a reference, a sum or a difference whose
  // first cell was typed as a date, a percentage or an amount, and a product or a quotient of
  // an amount, are shown as that cell is - as Excel carries a format over. Worked out in the
  // order the formulas are computed, so a formula's cells have theirs already.

  function padSheetFormatOf ( $sheet, $node, $depth ) {

    if ( $depth > 20 )
      return NULL;

    switch ( $node [0] ) {

      case 'f':
        if ( in_array ( $node [1], [ 'date', 'today', 'edate', 'eomonth', 'datevalue' ], TRUE ) ) return 'yyyy-mm-dd';
        if ( $node [1] == 'now' )                                                               return 'yyyy-mm-dd hh:mm';
        if ( in_array ( $node [1], [ 'sum', 'average', 'min', 'max', 'median', 'large', 'small' ], TRUE ) and isset ( $node [2] [0] ) )
          return padSheetFormatOf ( $sheet, $node [2] [0], $depth + 1 );
        return NULL;

      case 'r':
        return padSheetGet ( $sheet, $node [1], $node [2] ) ['format'] ?? NULL;

      case 'o':
        if ( $node [1] == '+' or $node [1] == '-' )
          return padSheetFormatOf ( $sheet, $node [2], $depth + 1 ) ?? padSheetFormatOf ( $sheet, $node [3], $depth + 1 );
        if ( $node [1] == '*' or $node [1] == '/' ) {
          $format = padSheetFormatOf ( $sheet, $node [2], $depth + 1 ) ?? ( $node [1] == '*' ? padSheetFormatOf ( $sheet, $node [3], $depth + 1 ) : NULL );
          return ( $format !== NULL and preg_match ( '/[' . padFormulaCurrencies . ']/u', $format ) ) ? $format : NULL;
        }
        return NULL;

      case 'u':
        return padSheetFormatOf ( $sheet, $node [2], $depth + 1 );

    }

    return NULL;

  }

  // The cells a page's PHP hands padSheet and padFormula: named - [ 'A1' => 10 ] - or a list
  // of rows of values; anything else is reported and taken as no cells.

  function padSheetCellsCheck ( $function, $cells ) {

    if ( ! $cells )
      return [];

    $named = TRUE;

    foreach ( array_keys ( $cells ) as $key )
      if ( ! is_string ( $key ) or ! padFormulaCellAt ( $key ) ) {
        $named = FALSE;
        break;
      }

    if ( $named )
      return $cells;

    $rows = array_is_list ( $cells );

    foreach ( $cells as $row )
      if ( ! is_array ( $row ) or ! array_is_list ( $row ) )
        $rows = FALSE;

    if ( $rows )
      return $cells;

    padError ( "$function: the cells are named - [ 'A1' => 10, 'A2' => '=A1*2' ] - or a list of rows of values" );

    return [];

  }

  // What a cell holds once computed: a formula's result, else its value.

  function padSheetResult ( $sheet, $column, $row ) {

    $cell = $sheet ['cells'] [$row] [$column] ?? NULL;

    if ( ! $cell )
      return NULL;

    return array_key_exists ( 'result', $cell ) ? $cell ['result'] : $cell ['value'];

  }

  // The surroundings of a formula evaluated beside a computed sheet, in no cell of its own.

  function padSheetEnv ( &$sheet, $seed = 0 ) {

    return [
      'width'  => $sheet ['width'],
      'height' => $sheet ['height'],
      'seed'   => $seed,
      'rand'   => 0,
      'at'     => NULL,
      'value'  => function ( $column, $row ) use ( &$sheet ) {
        return padSheetResult ( $sheet, $column, $row );
      }
    ];

  }

  // A value as its cell shows it: an error its code, an empty cell nothing, TRUE and FALSE,
  // a text as it is, a number through the format code - the column's, else the cell's own -
  // or General.

  function padSheetShow ( $value, $format ) {

    if ( padFormulaIsError ( $value ) ) return $value ['error'];
    if ( $value === NULL )              return '';
    if ( is_bool ( $value ) )           return $value ? 'TRUE' : 'FALSE';
    if ( is_string ( $value ) )         return $value;

    return ( $format !== NULL and $format !== '' ) ? padFormulaFormat ( (float) $value, $format ) : padFormulaGeneral ( (float) $value );

  }

  // A value as the PHP of a page gets it from padSheet and padFormula: a whole number as an
  // int, an error as its code, the rest as it is.

  function padSheetExport ( $value ) {

    if ( padFormulaIsError ( $value ) )
      return $value ['error'];

    if ( is_float ( $value ) ) {
      $value = padFormulaRound15 ( $value );
      if ( floor ( $value ) == $value and abs ( $value ) < 9007199254740992 )
        return (int) $value;
    }

    return $value;

  }

?>
