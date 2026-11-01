<?php

  // The formula language of the spreadsheet - =A1+B2*2, =SUM(B2:B9), =IF(C2>0,"yes","no") -
  // read by a parser of its own: it is spreadsheet syntax, not a PAD expression, so padEval
  // has no part in it. The functions are the files of subs/formulas/types/ - a kind each,
  // read with padKinds ( 'formulas' ) - and a formula names them in any case.
  //
  //   padFormulaTokens  the formula as tokens: numbers, "strings" with "" for a quote,
  //                     TRUE and FALSE, the error values, references - A1, $A$1, A1:B3,
  //                     whole columns A:C and rows 2:5 - function names, operators
  //   padFormulaParse   the tokens as a tree, by Excel's precedence, from loose to tight:
  //                     comparison (= <> < > <= >=), & (joins text), + -, * /, ^, the %
  //                     after a value, a - or + before one; ^ is read from the left, as
  //                     Excel reads it, so =2^3^2 is 64, and a minus before a value binds
  //                     tighter than ^, so =-2^2 is 4
  //   padFormulaEval    the value of a tree, in a sheet ($env: the cells, the formula's own
  //                     cell, the extent of the sheet)
  //
  // A value is a number (a float), a text, TRUE or FALSE, NULL for an empty cell, an error -
  // [ 'error' => '#DIV/0!' ] - or a range: [ 'range' => rows of values, 'c' => its first
  // column, 'r' => its first row, 'w' and 'h' its size ]. A range holds only the part of it
  // the sheet has cells in - A:A is the column as far as the sheet goes - and 'w' and 'h' say
  // how large it is, so COUNTBLANK counts the empty cells past the sheet too. A reference to
  // one cell is a range of one: a function tells a value typed into its call from one in a
  // cell, as Excel does - SUM("4") counts the 4, SUM(A1) holding the text "4" does not.
  //
  // An error is a value as any other and travels as Excel's do: an operator, a function that
  // takes a number, a reference to a cell holding one answer the first error they meet, left
  // to right; IFERROR and IFNA catch them. The values are Excel's - #DIV/0!, #N/A, #NAME?,
  // #NULL!, #NUM!, #REF!, #VALUE! - and #ERROR! for a formula that cannot be read at all,
  // which Excel would refuse to take: Google Sheets calls it that.
  //
  // There is no array evaluation: an operator, or a function that wants one value, takes the
  // one cell of a range Excel's implicit intersection gives. A function whose file says
  // 'arrays' - SUMPRODUCT - takes its arguments as arrays, and in them Excel computes an
  // operator or a function on every cell of a range: (A1:A9="x")*B1:B9. That is an error of
  // the formula where the parser sees it - an operator on a range of more than one cell - and
  // #VALUE! for the whole call wherever the evaluation meets it, never the number of one cell.

  class padFormulaNoArrays extends Exception {}

  const padFormulaErrorCodes = [ '#NULL!', '#DIV/0!', '#VALUE!', '#REF!', '#NAME?', '#NUM!', '#N/A' ];

  const padFormulaMaxColumn = 16384;      // XFD
  const padFormulaMaxRow    = 1048576;

  // The characters a text holds at most, as a cell of Excel does: a longer one is #VALUE!,
  // and the functions that make texts longer work out the length before they build one -
  // eight SUBSTITUTEs in each other built 200 million characters, nine asked for 2 GB.

  const padFormulaMaxText = 32767;

  // What an error value says in the title of its cell.

  const padFormulaErrorTexts = [
    '#NULL!'  => 'ranges that do not meet',
    '#DIV/0!' => 'a division by zero',
    '#VALUE!' => 'a value of the wrong kind',
    '#REF!'   => 'a reference that is not valid',
    '#NAME?'  => 'a name that is no function or cell',
    '#NUM!'   => 'a number that cannot be computed',
    '#N/A'    => 'a value that is not available',
    '#ERROR!' => 'a formula that cannot be read'
  ];

  function padFormulaError ( $code ) {

    return [ 'error' => $code ];

  }

  function padFormulaIsError ( $value ) {

    return is_array ( $value ) and isset ( $value ['error'] );

  }

  function padFormulaIsRange ( $value ) {

    return is_array ( $value ) and isset ( $value ['range'] );

  }

  // A column's letters as its number - A 1, Z 26, AA 27, XFD 16384 - and back.

  function padFormulaColumn ( $letters ) {

    $number = 0;

    foreach ( str_split ( strtoupper ( $letters ) ) as $letter )
      $number = $number * 26 + ord ( $letter ) - 64;

    return $number;

  }

  function padFormulaLetters ( $number ) {

    $letters = '';

    while ( $number > 0 ) {
      $number--;
      $letters = chr ( 65 + $number % 26 ) . $letters;
      $number  = intdiv ( $number, 26 );
    }

    return $letters;

  }

  function padFormulaCellName ( $column, $row ) {

    return padFormulaLetters ( $column ) . $row;

  }

  // A cell name - B12, $B$12 - as [ column, row ], or NULL.

  function padFormulaCellAt ( $name ) {

    if ( ! preg_match ( '/^\$?([A-Za-z]{1,3})\$?([1-9][0-9]{0,6})$/D', trim ( (string) $name ), $match ) )
      return NULL;

    $column = padFormulaColumn ( $match [1] );
    $row    = (int) $match [2];

    return ( $column <= padFormulaMaxColumn and $row <= padFormulaMaxRow ) ? [ $column, $row ] : NULL;

  }

  // The tokens of a formula, the = it starts with left off: each [ type, value, offset ] -
  // the offset of its first byte in the formula - and a reference also its text as written
  // and its form (cells, columns or rows), so a formula can be shifted (padSheetShift). A
  // formula that cannot be cut into tokens throws, with the place in characters, counted
  // from 1 at the =, for a message that points at it.

  function padFormulaTokens ( $formula ) {

    $text   = (string) $formula;
    $length = strlen ( $text );
    $at     = ( $length and $text [0] == '=' ) ? 1 : 0;
    $tokens = [];

    while ( $at < $length ) {

      $char = $text [$at];
      $from = $at;

      if ( $char == ' ' or $char == "\t" or $char == "\n" or $char == "\r" ) {
        $at++;
        continue;
      }

      if ( $char == '"' ) {

        $value = '';
        $at++;

        while ( TRUE ) {
          if ( $at >= $length )
            padFormulaThrow ( 'the text that starts here never ends - a " is missing', $text, $from );
          if ( $text [$at] == '"' ) {
            if ( $at + 1 < $length and $text [$at + 1] == '"' ) {
              $value .= '"';
              $at += 2;
              continue;
            }
            $at++;
            break;
          }
          $value .= $text [$at++];
        }

        $tokens [] = [ 'str', $value, $from ];
        continue;

      }

      if ( $char == '#' ) {

        foreach ( padFormulaErrorCodes as $code )
          if ( strcasecmp ( substr ( $text, $at, strlen ( $code ) ), $code ) == 0 ) {
            $tokens [] = [ 'err', $code, $from ];
            $at += strlen ( $code );
            continue 2;
          }

        padFormulaThrow ( 'no error value is written ' . padFormulaWord ( $text, $at ), $text, $from );

      }

      // A name followed straight by a bracket is a function - LOG10( is one, LOG10 alone is
      // a cell.

      if ( preg_match ( '/\G([A-Za-z_][A-Za-z0-9_.]*)\(/', $text, $match, 0, $at ) ) {
        $tokens [] = [ 'fn', $match [1], $from ];
        $at += strlen ( $match [0] );
        continue;
      }

      if ( preg_match ( '/\G(\$?)([A-Za-z]{1,3})(\$?)([1-9][0-9]{0,6})(?::(\$?)([A-Za-z]{1,3})(\$?)([1-9][0-9]{0,6}))?(?![A-Za-z0-9_.])/', $text, $match, 0, $at ) ) {

        $one = [ padFormulaColumn ( $match [2] ), (int) $match [4] ];
        $two = isset ( $match [6] ) ? [ padFormulaColumn ( $match [6] ), (int) $match [8] ] : $one;

        if ( max ( $one [0], $two [0] ) <= padFormulaMaxColumn and max ( $one [1], $two [1] ) <= padFormulaMaxRow ) {
          $tokens [] = [ 'ref', [ min ( $one [0], $two [0] ), min ( $one [1], $two [1] ),
                                  max ( $one [0], $two [0] ), max ( $one [1], $two [1] ) ], $from, $match [0], 'cells' ];
          $at += strlen ( $match [0] );
          continue;
        }

      }

      if ( preg_match ( '/\G(\$?)([A-Za-z]{1,3}):(\$?)([A-Za-z]{1,3})(?![A-Za-z0-9_.(])/', $text, $match, 0, $at ) ) {

        $one = padFormulaColumn ( $match [2] );
        $two = padFormulaColumn ( $match [4] );

        if ( max ( $one, $two ) <= padFormulaMaxColumn ) {
          $tokens [] = [ 'ref', [ min ( $one, $two ), 1, max ( $one, $two ), padFormulaMaxRow ], $from, $match [0], 'columns' ];
          $at += strlen ( $match [0] );
          continue;
        }

      }

      if ( preg_match ( '/\G(\$?)([1-9][0-9]{0,6}):(\$?)([1-9][0-9]{0,6})(?![0-9.A-Za-z])/', $text, $match, 0, $at ) ) {

        $one = (int) $match [2];
        $two = (int) $match [4];

        if ( max ( $one, $two ) <= padFormulaMaxRow ) {
          $tokens [] = [ 'ref', [ 1, min ( $one, $two ), padFormulaMaxColumn, max ( $one, $two ) ], $from, $match [0], 'rows' ];
          $at += strlen ( $match [0] );
          continue;
        }

      }

      if ( preg_match ( '/\G(?:[0-9]+(?:\.[0-9]*)?|\.[0-9]+)(?:[eE][+-]?[0-9]+)?/', $text, $match, 0, $at ) ) {
        $tokens [] = [ 'num', (float) $match [0], $from ];
        $at += strlen ( $match [0] );
        continue;
      }

      if ( preg_match ( '/\G(TRUE|FALSE)(?![A-Za-z0-9_.])/i', $text, $match, 0, $at ) ) {
        $tokens [] = [ 'bool', strtoupper ( $match [1] ) == 'TRUE', $from ];
        $at += strlen ( $match [0] );
        continue;
      }

      if ( preg_match ( '/\G[A-Za-z_\\\\][A-Za-z0-9_.]*/', $text, $match, 0, $at ) ) {
        $tokens [] = [ 'name', $match [0], $from ];
        $at += strlen ( $match [0] );
        continue;
      }

      $two = substr ( $text, $at, 2 );

      if ( $two == '<=' or $two == '>=' or $two == '<>' ) {
        $tokens [] = [ 'op', $two, $from ];
        $at += 2;
        continue;
      }

      if ( strpos ( '+-*/^&=<>%', $char ) !== FALSE ) {
        $tokens [] = [ 'op', $char, $from ];
        $at++;
        continue;
      }

      if ( $char == '(' or $char == ')' or $char == ',' ) {
        $tokens [] = [ $char, $char, $from ];
        $at++;
        continue;
      }

      // A value spliced into a formula written in the template travels with stand-ins for
      // its syntax characters (lib/encoding.php): it is text, and never part of the formula.

      if ( preg_match ( '/\G\xEE\x80[\x80-\xBF]/', $text, $match, 0, $at ) )
        padFormulaThrow ( 'a value spliced into the formula is text, never part of it', $text, $from );

      $character = mb_substr ( substr ( $text, $at ), 0, 1 );

      padFormulaThrow ( "the character $character has no meaning in a formula", $text, $from );

    }

    $tokens [] = [ 'end', '', $length ];

    return $tokens;

  }

  // The word that starts at a place, for a message.

  function padFormulaWord ( $text, $at ) {

    return preg_match ( '/\G[^\s,()]{1,20}/', $text, $match, 0, $at ) ? $match [0] : substr ( $text, $at, 1 );

  }

  // A formula that cannot be read: thrown with the place, in characters from 1 at the =.

  function padFormulaThrow ( $message, $text, $at ) {

    throw new Exception ( $message, mb_strlen ( substr ( $text, 0, $at ) ) + 1 );

  }

  // The tree of a formula, or throws. A node is [ kind, ... ]:
  //
  //   [ 'v', value ]               a number, a text, TRUE or FALSE, an error value
  //   [ 'r', c1, r1, c2, r2 ]      a reference, one cell when c1 = c2 and r1 = r2
  //   [ 'u', '-' or '+', node ]    a sign before a value
  //   [ '%', node ]                a value divided by 100
  //   [ 'o', operator, a, b ]      an operator between two values
  //   [ 'f', name, [ nodes ] ]     a function; an argument left empty - IF(A1,,2) - is [ 'm' ]
  //   [ 'x', name ]                a name that is no cell and no function: #NAME?
  //
  // $check: a function the formula calls that is no file of types/, or called with too few
  // or too many arguments, is an error of the formula - Excel takes neither - else it is
  // read as #NAME? and #N/A, as Google Sheets reads one it is given.

  function padFormulaParse ( $formula, $check = TRUE ) {

    $state = [ 'tokens' => padFormulaTokens ( $formula ), 'at' => 0, 'text' => (string) $formula, 'check' => $check ];

    if ( $state ['tokens'] [0] [0] == 'end' )
      padFormulaThrow ( 'the formula is empty', $state ['text'], strlen ( $state ['text'] ) );

    $tree = padFormulaCompare ( $state );
    $next = $state ['tokens'] [ $state ['at'] ];

    if ( $next [0] == ')' )
      padFormulaThrow ( 'this ) closes no (', $state ['text'], $next [2] );

    if ( $next [0] != 'end' )
      padFormulaThrow ( padFormulaShow ( $next ) . ' cannot follow what stands before it - an operator is missing', $state ['text'], $next [2] );

    return $tree;

  }

  function padFormulaShow ( $token ) {

    return match ( $token [0] ) {
      'str'   => 'the text "' . $token [1] . '"',
      'num'   => 'the number ' . padFormulaNumberText ( $token [1] ),
      'bool'  => $token [1] ? 'TRUE' : 'FALSE',
      'ref'   => 'a reference',
      'fn'    => strtoupper ( $token [1] ) . '(',
      'end'   => 'the end',
      default => $token [1]
    };

  }

  function padFormulaPeek ( &$state ) {

    return $state ['tokens'] [ $state ['at'] ];

  }

  function padFormulaBinary ( &$state, $operators, $next ) {

    $left = $next ( $state );

    while ( TRUE ) {
      $token = padFormulaPeek ( $state );
      if ( $token [0] != 'op' or ! in_array ( $token [1], $operators, TRUE ) )
        return $left;
      $state ['at']++;
      $left = [ 'o', $token [1], $left, $next ( $state ) ];
    }

  }

  function padFormulaCompare ( &$state ) {

    return padFormulaBinary ( $state, [ '=', '<>', '<', '>', '<=', '>=' ], 'padFormulaConcat' );

  }

  function padFormulaConcat ( &$state ) {

    return padFormulaBinary ( $state, [ '&' ], 'padFormulaAdd' );

  }

  function padFormulaAdd ( &$state ) {

    return padFormulaBinary ( $state, [ '+', '-' ], 'padFormulaMultiply' );

  }

  function padFormulaMultiply ( &$state ) {

    return padFormulaBinary ( $state, [ '*', '/' ], 'padFormulaPower' );

  }

  function padFormulaPower ( &$state ) {

    return padFormulaBinary ( $state, [ '^' ], 'padFormulaPercent' );

  }

  function padFormulaPercent ( &$state ) {

    $node = padFormulaUnary ( $state );

    while ( padFormulaPeek ( $state ) [0] == 'op' and padFormulaPeek ( $state ) [1] == '%' ) {
      $state ['at']++;
      $node = [ '%', $node ];
    }

    return $node;

  }

  function padFormulaUnary ( &$state ) {

    $token = padFormulaPeek ( $state );

    if ( $token [0] == 'op' and ( $token [1] == '-' or $token [1] == '+' ) ) {
      $state ['at']++;
      return [ 'u', $token [1], padFormulaUnary ( $state ) ];
    }

    return padFormulaPrimary ( $state );

  }

  function padFormulaPrimary ( &$state ) {

    $token = padFormulaPeek ( $state );
    $text  = $state ['text'];

    switch ( $token [0] ) {

      case 'num':
        $state ['at']++;
        return [ 'v', padFormulaKept ( $token [1] ) ?? padFormulaError ( '#NUM!' ) ];

      case 'str':
      case 'bool':
        $state ['at']++;
        return [ 'v', $token [1] ];

      case 'err':
        $state ['at']++;
        return [ 'v', padFormulaError ( $token [1] ) ];

      case 'ref':
        $state ['at']++;
        return [ 'r', ...$token [1] ];

      case 'name':
        $state ['at']++;
        if ( $state ['check'] )
          padFormulaThrow ( "the name {$token [1]} is no cell, no function and no value", $text, $token [2] );
        return [ 'x', $token [1] ];

      case 'fn':
        $state ['at']++;
        return padFormulaCall ( $state, $token );

      case '(':
        $state ['at']++;
        $node  = padFormulaCompare ( $state );
        $close = padFormulaPeek ( $state );
        if ( $close [0] != ')' )
          padFormulaThrow ( $close [0] == 'end' ? 'the ( here is never closed' : padFormulaShow ( $close ) . ' stands where the ( here should close',
                            $text, $close [0] == 'end' ? $token [2] : $close [2] );
        $state ['at']++;
        return $node;

      case 'end':
        padFormulaThrow ( 'a value is missing at the end', $text, $token [2] );

      default:
        padFormulaThrow ( 'a value is missing before ' . padFormulaShow ( $token ), $text, $token [2] );

    }

  }

  // A function and its arguments: an argument may be left empty, IF(A1,,0), and the call
  // checked against what its file of types/ says it takes.

  function padFormulaCall ( &$state, $token ) {

    $name = strtolower ( $token [1] );
    $args = [];
    $text = $state ['text'];

    if ( padFormulaPeek ( $state ) [0] == ')' )
      $state ['at']++;
    else
      while ( TRUE ) {
        $next = padFormulaPeek ( $state );
        if ( $next [0] == 'end' )
          padFormulaThrow ( 'the ( of ' . strtoupper ( $name ) . ' here is never closed', $text, $token [2] );
        if ( $next [0] == ',' or $next [0] == ')' )
          $args [] = [ 'm' ];
        else
          $args [] = padFormulaCompare ( $state );
        $next = padFormulaPeek ( $state );
        $state ['at']++;
        if ( $next [0] == ')' )
          break;
        if ( $next [0] == ',' )
          continue;
        if ( $next [0] == 'end' )
          padFormulaThrow ( 'the ( of ' . strtoupper ( $name ) . ' here is never closed', $text, $token [2] );
        padFormulaThrow ( padFormulaShow ( $next ) . ' stands where a , or the ) of ' . strtoupper ( $name ) . ' should', $text, $next [2] );
      }

    $type = padFormulaType ( $name );

    if ( ! $type ) {
      if ( $state ['check'] )
        padFormulaThrow ( strtoupper ( $name ) . ' is no function - the functions are the files of pad/subs/formulas/types/', $text, $token [2] );
      return [ 'x', $token [1] ];
    }

    [ $min, $max ] = $type ['args'];

    if ( count ( $args ) < $min or count ( $args ) > $max or ( isset ( $type ['pairs'] ) and ( count ( $args ) - $type ['pairs'] ) % 2 ) ) {
      if ( $state ['check'] )
        padFormulaThrow ( strtoupper ( $name ) . ' takes ' . padFormulaArity ( $type ) . ', not ' . count ( $args ), $text, $token [2] );
      return [ 'v', padFormulaError ( '#N/A' ) ];
    }

    if ( isset ( $type ['arrays'] ) and ( $range = padFormulaArrayOperand ( [ 'f', $name, $args ], FALSE ) ) !== NULL ) {
      if ( $state ['check'] )
        padFormulaThrow ( strtoupper ( $name ) . " takes ranges as they are - an operator on the range $range asks for"
                        . ' array evaluation, which the formulas do not have; SUMIFS and COUNTIFS add up and count with criteria', $text, $token [2] );
      return [ 'v', padFormulaError ( '#VALUE!' ) ];
    }

    return [ 'f', $name, $args ];

  }

  // The first range of more than one cell an operator stands on in a tree, as text - A1:A9 -
  // or NULL: in the arguments of a function that takes arrays it asks for array evaluation.

  function padFormulaArrayOperand ( $node, $operand ) {

    switch ( $node [0] ) {

      case 'r':
        if ( $operand and ( $node [1] != $node [3] or $node [2] != $node [4] ) )
          return padFormulaCellName ( $node [1], $node [2] ) . ':' . padFormulaCellName ( $node [3], $node [4] );
        return NULL;

      case 'o': return padFormulaArrayOperand ( $node [2], TRUE ) ?? padFormulaArrayOperand ( $node [3], TRUE );
      case 'u': return padFormulaArrayOperand ( $node [2], TRUE );
      case '%': return padFormulaArrayOperand ( $node [1], TRUE );

      case 'f':
        foreach ( $node [2] as $arg )
          if ( ( $range = padFormulaArrayOperand ( $arg, FALSE ) ) !== NULL )
            return $range;
        return NULL;

    }

    return NULL;

  }

  function padFormulaArity ( $type ) {

    [ $min, $max ] = $type ['args'];

    if ( isset ( $type ['takes'] ) )                    return $type ['takes'];
    if ( $min == 0 and $max == 0 )                      return 'no arguments';
    if ( $min == $max )                                 return $min == 1 ? '1 argument' : "$min arguments";
    if ( $max >= 255 )                                  return 'at least ' . ( $min == 1 ? '1 argument' : "$min arguments" );

    return "$min to $max arguments";

  }

  // The file of a function, as it returns it: [ 'args' => [ min, max ], 'call' => fn, ... ],
  // or NULL for a name that is no file of types/.

  function padFormulaType ( $name ) {

    static $types = [];

    if ( ! array_key_exists ( $name, $types ) )
      $types [$name] = padKind ( 'formulas', $name ) ? include PAD . "subs/formulas/types/$name.php" : NULL;

    return $types [$name];

  }

  // The names of the functions a tree calls, for the reference record.

  function padFormulaCalls ( $node, &$names ) {

    if ( $node [0] == 'f' ) {
      $names [ $node [1] ] = TRUE;
      foreach ( $node [2] as $arg )
        padFormulaCalls ( $arg, $names );
    } elseif ( $node [0] == 'o' ) {
      padFormulaCalls ( $node [2], $names );
      padFormulaCalls ( $node [3], $names );
    } elseif ( $node [0] == 'u' ) {
      padFormulaCalls ( $node [2], $names );
    } elseif ( $node [0] == '%' ) {
      padFormulaCalls ( $node [1], $names );
    }

  }

  // The references of a tree: [ c1, r1, c2, r2 ] each.

  function padFormulaRefs ( $node, &$refs ) {

    switch ( $node [0] ) {
      case 'r': $refs [] = [ $node [1], $node [2], $node [3], $node [4] ]; break;
      case 'f': foreach ( $node [2] as $arg ) padFormulaRefs ( $arg, $refs ); break;
      case 'o': padFormulaRefs ( $node [2], $refs ); padFormulaRefs ( $node [3], $refs ); break;
      case 'u': padFormulaRefs ( $node [2], $refs ); break;
      case '%': padFormulaRefs ( $node [1], $refs ); break;
    }

  }

  // The value of a tree. $env: 'value' a function ( column, row ) answering what a cell
  // holds - computed already, the sheet is worked through in the order its formulas need -
  // 'at' the formula's own cell [ column, row ] or NULL, 'width' and 'height' how far the
  // sheet goes, 'seed' the seed of RAND and 'rand' how many it gave so far.

  function padFormulaEval ( $node, &$env ) {

    switch ( $node [0] ) {

      case 'v': return $node [1];

      case 'm': return NULL;

      case 'x': return padFormulaError ( '#NAME?' );

      case 'r': return padFormulaRange ( $node [1], $node [2], $node [3], $node [4], $env );

      case 'u':
        $value = padFormulaNumber ( padFormulaScalar ( padFormulaEval ( $node [2], $env ), $env ) );
        if ( padFormulaIsError ( $value ) )
          return $value;
        return $node [1] == '-' ? -$value : $value;

      case '%':
        $value = padFormulaNumber ( padFormulaScalar ( padFormulaEval ( $node [1], $env ), $env ) );
        return padFormulaIsError ( $value ) ? $value : padFormulaKept ( $value / 100 );

      case 'o':
        return padFormulaOperator ( $node [1], padFormulaScalar ( padFormulaEval ( $node [2], $env ), $env ),
                                                padFormulaScalar ( padFormulaEval ( $node [3], $env ), $env ) );

      case 'f':
        $args = [];
        if ( isset ( padFormulaType ( $node [1] ) ['arrays'] ) and empty ( $env ['arrays'] ) ) {
          $env ['arrays'] = TRUE;
          try {
            foreach ( $node [2] as $arg )
              $args [] = padFormulaEval ( $arg, $env );
          } catch ( padFormulaNoArrays $stop ) {
            return padFormulaError ( '#VALUE!' );
          } finally {
            $env ['arrays'] = FALSE;
          }
        } else
          foreach ( $node [2] as $arg )
            $args [] = padFormulaEval ( $arg, $env );
        // A function that still divides by 0 on some number - PHP throws where a spreadsheet
        // answers an error value - answers #NUM!: a visitor's input ends no request.
        try {
          $result = padFormulaType ( $node [1] ) ['call'] ( $args, $env );
        } catch ( ArithmeticError $error ) {
          return padFormulaError ( '#NUM!' );
        }
        if ( is_string ( $result ) and padFormulaTooLong ( $result ) )
          return padFormulaError ( '#VALUE!' );
        return is_float ( $result ) ? ( padFormulaKept ( $result ) ?? padFormulaError ( '#NUM!' ) ) : $result;

    }

    return padFormulaError ( '#VALUE!' );

  }

  // The cells of a reference, as far as the sheet has them.

  function padFormulaRange ( $c1, $r1, $c2, $r2, &$env ) {

    $rows = [];
    $last = min ( $r2, $env ['height'] ?? 0 );
    $end  = min ( $c2, $env ['width']  ?? 0 );

    for ( $r = $r1; $r <= $last; $r++ ) {
      $row = [];
      for ( $c = $c1; $c <= $end; $c++ )
        $row [] = $env ['value'] ( $c, $r );
      $rows [] = $row;
    }

    return [ 'range' => $rows, 'c' => $c1, 'r' => $r1, 'w' => $c2 - $c1 + 1, 'h' => $r2 - $r1 + 1 ];

  }

  // The one value a range stands for where an operator wants one: the cell of a range of
  // one, else the cell of the range in the formula's own row (a column) or column (a row) -
  // Excel's implicit intersection - else #VALUE!. In the arguments of a function that takes
  // arrays Excel would take every cell: the call stops there, and answers #VALUE! - an error
  // value here would be caught by an IFERROR in it and turned into a number.

  function padFormulaScalar ( $value, $env ) {

    if ( ! padFormulaIsRange ( $value ) )
      return $value;

    if ( $value ['w'] == 1 and $value ['h'] == 1 )
      return padFormulaCell ( $value, 0, 0 );

    if ( ! empty ( $env ['arrays'] ) )
      throw new padFormulaNoArrays;

    $at = $env ['at'] ?? NULL;

    if ( $at and $value ['w'] == 1 and $at [1] >= $value ['r'] and $at [1] < $value ['r'] + $value ['h'] )
      return padFormulaCell ( $value, $at [1] - $value ['r'], 0 );

    if ( $at and $value ['h'] == 1 and $at [0] >= $value ['c'] and $at [0] < $value ['c'] + $value ['w'] )
      return padFormulaCell ( $value, 0, $at [0] - $value ['c'] );

    return padFormulaError ( '#VALUE!' );

  }

  // A cell of a range by its place in it, from 0 - empty past the sheet's cells.

  function padFormulaCell ( $range, $row, $column ) {

    return $range ['range'] [$row] [$column] ?? NULL;

  }

  // Every value of a range, row by row, as far as the sheet has cells: the empty cells past
  // it - which no value holds - are counted by padFormulaRangeSize less what this gives.

  function padFormulaFlat ( $range ) {

    $flat = [];

    foreach ( $range ['range'] as $row )
      foreach ( $row as $value )
        $flat [] = $value;

    return $flat;

  }

  function padFormulaRangeSize ( $range ) {

    return $range ['w'] * $range ['h'];

  }

  // A value as a number, as an operator takes it: an empty cell is 0, TRUE 1, a text that
  // reads as a number - 5, 1,250.50, 12%, $4, a date - that number, else #VALUE!.

  function padFormulaNumber ( $value ) {

    if ( $value === NULL )                       return 0.0;
    if ( is_bool ( $value ) )                    return $value ? 1.0 : 0.0;
    if ( is_int ( $value ) or is_float ( $value ) ) return (float) $value;
    if ( padFormulaIsError ( $value ) )          return $value;
    if ( padFormulaIsRange ( $value ) )          return padFormulaError ( '#VALUE!' );

    $number = padFormulaParseValue ( (string) $value );

    return $number === NULL ? padFormulaError ( '#VALUE!' ) : $number;

  }

  // A value as a text: a number as Excel writes it in text - up to 15 digits - TRUE and FALSE
  // in capitals, an empty cell nothing.

  function padFormulaText ( $value ) {

    if ( $value === NULL )                       return '';
    if ( is_bool ( $value ) )                    return $value ? 'TRUE' : 'FALSE';
    if ( is_int ( $value ) or is_float ( $value ) ) return padFormulaNumberText ( (float) $value );
    if ( padFormulaIsError ( $value ) )          return $value;
    if ( padFormulaIsRange ( $value ) )          return padFormulaError ( '#VALUE!' );

    return (string) $value;

  }

  // A value as TRUE or FALSE: a number is TRUE when it is not 0, the texts TRUE and FALSE in
  // any case are themselves, an empty cell is FALSE, any other text #VALUE!.

  function padFormulaBool ( $value ) {

    if ( $value === NULL )                       return FALSE;
    if ( is_bool ( $value ) )                    return $value;
    if ( is_int ( $value ) or is_float ( $value ) ) return $value != 0;
    if ( padFormulaIsError ( $value ) )          return $value;

    $word = strtoupper ( trim ( (string) $value ) );

    if ( $word === 'TRUE' )  return TRUE;
    if ( $word === 'FALSE' ) return FALSE;

    return padFormulaError ( '#VALUE!' );

  }

  // An argument as one value - a range of one, or the range's cell in the formula's row or
  // column - and that as a number, a text or a logical value: the first error met is the
  // answer.

  function padFormulaArgNumber ( $value, $env ) {

    return padFormulaNumber ( padFormulaScalar ( $value, $env ) );

  }

  function padFormulaArgText ( $value, $env ) {

    return padFormulaText ( padFormulaScalar ( $value, $env ) );

  }

  function padFormulaArgBool ( $value, $env ) {

    return padFormulaBool ( padFormulaScalar ( $value, $env ) );

  }

  // The first error among values, or NULL.

  function padFormulaFirstError ( ...$values ) {

    foreach ( $values as $value )
      if ( padFormulaIsError ( $value ) )
        return $value;

    return NULL;

  }

  // An operator between two values, left first: an error on the left is the answer before
  // one on the right.

  function padFormulaOperator ( $operator, $a, $b ) {

    if ( padFormulaIsError ( $a ) ) return $a;
    if ( padFormulaIsError ( $b ) ) return $b;

    if ( $operator == '&' ) {
      $a = padFormulaText ( $a );
      $b = padFormulaText ( $b );
      if ( $error = padFormulaFirstError ( $a, $b ) )
        return $error;
      return padFormulaTooLong ( $a, $b ) ? padFormulaError ( '#VALUE!' ) : $a . $b;
    }

    if ( in_array ( $operator, [ '=', '<>', '<', '>', '<=', '>=' ], TRUE ) ) {
      $order = padFormulaOrder ( $a, $b );
      return match ( $operator ) {
        '='  => $order == 0,
        '<>' => $order != 0,
        '<'  => $order < 0,
        '>'  => $order > 0,
        '<=' => $order <= 0,
        '>=' => $order >= 0
      };
    }

    $a = padFormulaNumber ( $a );
    $b = padFormulaNumber ( $b );

    if ( $error = padFormulaFirstError ( $a, $b ) )
      return $error;

    $result = match ( $operator ) {
      '+' => padFormulaNearZero ( $a + $b, $a, $b ),
      '-' => padFormulaNearZero ( $a - $b, $a, $b ),
      '*' => $a * $b,
      '/' => $b == 0 ? padFormulaError ( '#DIV/0!' ) : $a / $b,
      '^' => padFormulaPow ( $a, $b )
    };

    return is_float ( $result ) ? ( padFormulaKept ( $result ) ?? padFormulaError ( '#NUM!' ) ) : $result;

  }

  // A number as a spreadsheet keeps it, or NULL: past the largest a float holds - INF, and
  // NAN - is no number, as Excel holds none past 9.99999999999999E+307; one below the
  // smallest normal float, 2.2251E-308, is 0, as Excel makes it. A number past either end
  // never reaches a cell: the General form divided by a power of ten that was 0, and cast a
  // number past an int.

  function padFormulaKept ( $number ) {

    if ( ! is_finite ( $number ) )
      return NULL;

    return abs ( $number ) < PHP_FLOAT_MIN ? 0.0 : (float) $number;

  }

  // Whether texts together hold more characters than a text may - counted only where their
  // bytes are more, which most texts are not.

  function padFormulaTooLong ( ...$texts ) {

    if ( array_sum ( array_map ( 'strlen', $texts ) ) <= padFormulaMaxText )
      return FALSE;

    return array_sum ( array_map ( 'mb_strlen', $texts ) ) > padFormulaMaxText;

  }

  // A sum or a difference that is no more than the last bits of its operands is 0, as Excel
  // makes it: =0.1+0.2-0.3 is 0, not 5.55E-17.

  function padFormulaNearZero ( $result, $a, $b ) {

    return ( $result != 0 and abs ( $result ) < max ( abs ( $a ), abs ( $b ) ) * 1e-15 ) ? 0.0 : $result;

  }

  // ^ and POWER: 0^0 and a negative number to a fraction are #NUM!, 0 to a negative power
  // #DIV/0!.

  function padFormulaPow ( $base, $exponent ) {

    if ( $base == 0 and $exponent == 0 ) return padFormulaError ( '#NUM!' );
    if ( $base == 0 and $exponent < 0 )  return padFormulaError ( '#DIV/0!' );
    if ( $base < 0 and floor ( $exponent ) != $exponent ) return padFormulaError ( '#NUM!' );

    $result = $base ** $exponent;

    return is_finite ( $result ) ? (float) $result : padFormulaError ( '#NUM!' );

  }

  // How two values order, as Excel's comparisons see them: numbers before texts before
  // logical values, a text without regard to case, an empty cell as 0, "" or FALSE - what
  // the other side is. Numbers compare as they show, to 15 digits: =0.1+0.2=0.3 is TRUE.

  function padFormulaOrder ( $a, $b ) {

    if ( $a === NULL and $b === NULL )
      return 0;

    if ( $a === NULL ) $a = is_string ( $b ) ? '' : ( is_bool ( $b ) ? FALSE : 0.0 );
    if ( $b === NULL ) $b = is_string ( $a ) ? '' : ( is_bool ( $a ) ? FALSE : 0.0 );

    $rank = fn ( $value ) => is_bool ( $value ) ? 2 : ( is_string ( $value ) ? 1 : 0 );

    if ( $rank ( $a ) != $rank ( $b ) )
      return $rank ( $a ) <=> $rank ( $b );

    if ( is_string ( $a ) )
      return padFormulaTextOrder ( $a, $b );

    if ( is_bool ( $a ) )
      return (int) $a <=> (int) $b;

    return padFormulaRound15 ( (float) $a ) <=> padFormulaRound15 ( (float) $b );

  }

  function padFormulaTextOrder ( $a, $b ) {

    return strcmp ( mb_strtolower ( $a ), mb_strtolower ( $b ) ) <=> 0;

  }

  // A number to the 15 significant digits a spreadsheet keeps - one below the smallest
  // normal float 0, as padFormulaKept makes it.

  function padFormulaRound15 ( $number ) {

    if ( abs ( $number ) < PHP_FLOAT_MIN )
      return 0.0;

    return ! is_finite ( $number ) ? (float) $number : (float) sprintf ( '%.14e', $number );

  }

  // A number rounded to a number of decimals - fewer than 0 rounds to tens, hundreds - on its
  // 15 significant digits, as Excel rounds: half away from zero ('half', ROUND), away from
  // zero ('up', ROUNDUP) or toward it ('down', ROUNDDOWN). ROUND(1.005,2) is 1.01, though
  // 1.005 is a little less than that as a binary number.

  function padFormulaRoundTo ( $number, $digits, $mode = 'half' ) {

    if ( $number == 0 or ! is_finite ( $number ) )
      return 0.0;

    $digits = (int) $digits;

    [ $mantissa, $exponent ] = explode ( 'e', sprintf ( '%.14e', abs ( $number ) ) );

    $all  = str_replace ( '.', '', $mantissa );
    $keep = (int) $exponent + 1 + $digits;
    $sign = $number < 0 ? -1 : 1;

    if ( $keep >= 15 )
      return $sign * (float) ( $all . 'e' . ( (int) $exponent - 14 ) );

    if ( $keep < 0 )
      return $mode == 'up' ? $sign * (float) ( '1e' . ( -$digits ) ) : 0.0;

    $head = substr ( $all, 0, $keep );
    $rest = substr ( $all, $keep );
    $up   = match ( $mode ) {
      'half'  => $rest [0] >= '5',
      'up'    => trim ( $rest, '0' ) !== '',
      default => FALSE
    };

    if ( $up )
      $head = padFormulaIncrement ( $head );

    if ( $head === '' or trim ( $head, '0' ) === '' )
      return 0.0;

    return $sign * (float) ( $head . 'e' . ( -$digits ) );

  }

  // A string of digits plus one: 199 is 200, 99 is 100, '' is 1.

  function padFormulaIncrement ( $digits ) {

    $at = strlen ( $digits ) - 1;

    while ( $at >= 0 and $digits [$at] == '9' ) {
      $digits [$at] = '0';
      $at--;
    }

    return $at < 0 ? '1' . $digits : substr_replace ( $digits, (string) ( (int) $digits [$at] + 1 ), $at, 1 );

  }

  // A number as Excel writes it in text - =A1&"" - up to 15 significant digits, no trailing
  // zeros, written with an exponent when it is 1E+15 or more, or less than 1E-9.

  function padFormulaNumberText ( $number ) {

    if ( $number == 0 )
      return '0';

    if ( ! is_finite ( $number ) )
      return '#NUM!';

    [ $mantissa, $exponent ] = explode ( 'e', sprintf ( '%.14e', abs ( $number ) ) );

    $digits   = rtrim ( str_replace ( '.', '', $mantissa ), '0' );
    $exponent = (int) $exponent;
    $sign     = $number < 0 ? '-' : '';

    if ( $exponent >= 15 or $exponent < -9 )
      return $sign . $digits [0] . ( strlen ( $digits ) > 1 ? '.' . substr ( $digits, 1 ) : '' )
           . 'E' . ( $exponent < 0 ? '-' : '+' ) . str_pad ( (string) abs ( $exponent ), 2, '0', STR_PAD_LEFT );

    return $sign . padFormulaPlace ( $digits, $exponent );

  }

  // Significant digits with the first standing at 10^exponent, written plainly.

  function padFormulaPlace ( $digits, $exponent ) {

    if ( $exponent < 0 )
      return '0.' . str_repeat ( '0', -$exponent - 1 ) . $digits;

    if ( strlen ( $digits ) <= $exponent + 1 )
      return $digits . str_repeat ( '0', $exponent + 1 - strlen ( $digits ) );

    return substr ( $digits, 0, $exponent + 1 ) . '.' . substr ( $digits, $exponent + 1 );

  }

  // RAND: a number from 0 up to 1, the same for the same seed, cell and call - a sheet that
  // gave other numbers on every request could never be compared with what it gave before.

  function padFormulaRandom ( &$env ) {

    $env ['rand'] = ( $env ['rand'] ?? 0 ) + 1;

    $at   = $env ['at'] ?? [ 0, 0 ];
    $hash = md5 ( ( $env ['seed'] ?? 0 ) . ':' . $at [0] . ':' . $at [1] . ':' . $env ['rand'] );

    return hexdec ( substr ( $hash, 0, 13 ) ) / 4503599627370496;

  }

?>
