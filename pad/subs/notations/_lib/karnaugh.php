<?php

  // Karnaugh maps - the karnaugh notation, {notation:karnaugh 'm(6,8,9,10,11,12,13,14)'/}. A Boolean
  // function of two to four variables drawn as its Karnaugh map: the cells in Gray-code order,
  // so two cells next to each other - over the edges as well - differ in one variable, each
  // with its value, 1, 0 or a don't care; the groups of a minimal sum of products looped round
  // in colours of their own, and the minimal expression under it in MathML.
  //
  // padKarnaughRead     the function of a text: its name, its variables and the value of every
  //                     cell - from minterms m(1,3,5) and don't cares d(0), from maxterms
  //                     M(0,2), from the column of a truth table 0110 1001, or from an expression,
  //                     A'B + AC, ~(A ^ B), A and not C - or what it cannot read
  // padKarnaughPrimes   the prime implicants: every cube of cells that holds only the values
  //                     grouped and don't cares, and no greater one that does
  // padKarnaughCover    a minimal cover of the cells: the fewest groups, then the fewest
  //                     literals - the essential groups first, then the smallest set of the rest
  // padKarnaughSvg      the map with its groups
  //
  // The cubes are found by trying every one - 3^n of them, 81 for four variables - so what the
  // map calls prime is prime, and the cover is searched among all sets of the primes in order of
  // their size, so it is minimal; the expression is checked against every cell before it is
  // written. Wikipedia's example of the article "Karnaugh map", Σm(6, 8, 9, 10, 11, 12, 13, 14),
  // gives AC̄ + AB̄ + BCD̄ and as a product of sums (A + B)(A + C)(B̄ + C̄ + D̄); with 15 a don't
  // care A + BCD̄ and (A + B)(A + C)(A + D̄), as the article says.

  include_once PAD . 'subs/notations/_lib/math.php';

  const PAD_KARNAUGH_COLOURS = [ 'line'  => [ '#4b5563', '#9ca3af' ], 'text' => [ '#111827', '#f3f4f6' ],
                                 'dim'   => [ '#9ca3af', '#6b7280' ], 'head' => [ '#4b5563', '#9ca3af' ],
                                 'cell'  => [ '#ffffff', '#1f2937' ],
                                 'g1'    => [ '#0072b2', '#56b4e9' ], 'g2' => [ '#d55e00', '#f0a35e' ],
                                 'g3'    => [ '#009e73', '#3fd3a6' ], 'g4' => [ '#cc79a7', '#e7a6cb' ],
                                 'g5'    => [ '#b38600', '#f0c75e' ], 'g6' => [ '#5b5bd6', '#9d9df2' ] ];

  const PAD_KARNAUGH_LONGEST = 2000;

  // The Gray-code order of one and of two variables.

  const PAD_KARNAUGH_GRAY = [ 1 => [ 0, 1 ], 2 => [ 0, 1, 3, 2 ] ];

  // The function of a text: [ name, variables, cells - index => '1', '0' or 'x' - ] or a string,
  // what is wrong. The variables are vars= when given; else those of the expression in their
  // natural order, or A, B, C, D as many as the minterms or the table need. A text may start
  // with its name, F = ..., or its name and variables, F(A, B, C, D) = ...

  function padKarnaughRead ( $text, $given ) {

    $text = trim ( (string) $text );
    $name = 'F';
    $vars = NULL;

    if ( $given !== '' ) {
      $vars = padKarnaughNames ( $given );
      if ( is_string ( $vars ) )
        return "vars='" . padNotationShow ( $given ) . "' $vars";
    }

    if ( preg_match ( '/^([A-Za-z]\w*)\s*(?:\(([^()]*)\))?\s*=\s*(.*)$/su', $text, $m ) ) {
      $name = $m [1];
      $text = trim ( $m [3] );
      if ( ( $m [2] ?? '' ) !== '' ) {
        $named = padKarnaughNames ( $m [2] );
        if ( is_string ( $named ) )
          return "variables ($m[2]) $named";
        if ( $vars !== NULL and $vars !== $named )
          return "the variables " . implode ( ', ', $named ) . " where vars= gives " . implode ( ', ', $vars );
        $vars = $named;
      }
    }

    // Minterms and don't cares: Σm(1,3,5) + d(0,2); maxterms: ΠM(0,2) + d(1).

    if ( preg_match ( '/^(Σ|∑|sum|Π|∏|prod)?\s*(m|M)\s*\(([^()]*)\)\s*(?:[+,]?\s*(?:d|dc)\s*\(([^()]*)\))?$/u', $text, $m ) ) {
      $maxterms = ( in_array ( $m [1], [ 'Π', '∏', 'prod' ] ) or ( $m [1] === '' and $m [2] === 'M' ) );
      $listed   = padKarnaughNumbers ( $m [3] );
      $cares    = padKarnaughNumbers ( $m [4] ?? '' );
      if ( is_string ( $listed ) or is_string ( $cares ) )
        return is_string ( $listed ) ? $listed : $cares;
      $highest = max ( [ 0, ...$listed, ...$cares ] );
      $count   = $vars ? count ( $vars ) : max ( 2, strlen ( decbin ( $highest ) ) );
      if ( $count > 4 )
        return "a term $highest - a map of 2 to 4 variables numbers its cells 0 to 15";
      if ( $highest >= 2 ** $count )
        return "a term $highest - a map of $count variables numbers its cells 0 to " . ( 2 ** $count - 1 );
      if ( $both = array_intersect ( $listed, $cares ) )
        return "the term " . reset ( $both ) . " both " . ( $maxterms ? 'a maxterm' : 'a minterm' ) . " and a don't care";
      $cells = array_fill ( 0, 2 ** $count, $maxterms ? '1' : '0' );
      foreach ( $listed as $one )
        $cells [$one] = $maxterms ? '0' : '1';
      foreach ( $cares as $one )
        $cells [$one] = 'x';
      return [ $name, $vars ?? array_slice ( [ 'A', 'B', 'C', 'D' ], 0, $count ), $cells ];
    }

    // The output column of a truth table, the row of A = B = ... = 0 first - unless it is a
    // product of variables named x and digits, which a column's don't care x may look like.

    $column = preg_replace ( '/\s+/u', '', $text );

    if ( strlen ( $column ) > 1 and preg_match ( '/^[01xX\-]+$/', $column ) and ! padKarnaughProduct ( $text, $vars ) ) {
      $count = array_search ( strlen ( $column ), [ 2 => 4, 3 => 8, 4 => 16 ], TRUE );
      if ( $count === FALSE )
        return "a truth table of " . strlen ( $column ) . " values - a map of 2, 3 or 4 variables takes 4, 8 or 16";
      if ( $vars and count ( $vars ) != $count )
        return "a truth table of " . strlen ( $column ) . " values for " . count ( $vars ) . " variables";
      $cells = array_map ( fn ( $one ) => ( $one === '0' or $one === '1' ) ? $one : 'x', str_split ( $column ) );
      return [ $name, $vars ?? array_slice ( [ 'A', 'B', 'C', 'D' ], 0, $count ), $cells ];
    }

    // An expression.

    $tree = padKarnaughParse ( $text );

    if ( is_string ( $tree ) )
      return $tree;

    $used = padKarnaughUsed ( $tree );

    if ( $vars === NULL ) {
      $vars = array_keys ( $used );
      usort ( $vars, 'strnatcmp' );
    } elseif ( $missing = array_diff ( array_keys ( $used ), $vars ) )
      return "an expression with the variable " . reset ( $missing ) . " that is none of " . implode ( ', ', $vars );

    if ( count ( $vars ) < 2 or count ( $vars ) > 4 )
      return count ( $vars ) . ' ' . ( count ( $vars ) == 1 ? 'variable' : 'variables' ) . " - a map has 2 to 4, vars='A, B' names them";

    $cells = [];

    for ( $index = 0; $index < 2 ** count ( $vars ); $index++ ) {
      $values = [];
      foreach ( $vars as $at => $var )
        $values [$var] = ( $index >> ( count ( $vars ) - 1 - $at ) ) & 1;
      $cells [$index] = padKarnaughEval ( $tree, $values ) ? '1' : '0';
    }

    return [ $name, $vars, $cells ];

  }

  // A text of x and the digits 0 and 1 alone that reads as variables - every x with digits
  // after it, no digit standing alone, no name twice: x0 x1, x1x0, x01 x10 x11 - is the
  // product of them when it names two to four, or when every one is among the variables
  // named, vars= or F(x0, x1); else its x is a don't care of a column - 10xx, x0x0, x1x0x1x0,
  // and 1x01 for x0 and x1.

  function padKarnaughProduct ( $text, $vars ) {

    if ( ! preg_match ( '/^\s*(?:[xX][01]+\s*)+$/D', $text ) )
      return FALSE;

    preg_match_all ( '/[xX][01]+/', $text, $names );

    $names = $names [0];

    if ( count ( array_unique ( $names ) ) != count ( $names ) )
      return FALSE;

    if ( $vars !== NULL )
      return ! array_diff ( $names, $vars );

    return count ( $names ) >= 2 and count ( $names ) <= 4;

  }

  // Variable names: a letter, digits after it - A, x1 - two to four of them, each once.

  function padKarnaughNames ( $text ) {

    $names = preg_split ( '/[\s,]+/u', trim ( (string) $text ), -1, PREG_SPLIT_NO_EMPTY );

    foreach ( $names as $name )
      if ( ! preg_match ( '/^[A-Za-z]\d*$/D', $name ) )
        return "has a variable '" . padNotationShow ( $name ) . "' - a variable is a letter, digits after it: A, x1";

    if ( count ( $names ) < 2 or count ( $names ) > 4 )
      return "names " . count ( $names ) . " - a map has 2 to 4 variables";

    if ( count ( array_unique ( $names ) ) != count ( $names ) )
      return "names a variable twice";

    return $names;

  }

  // The numbers of m( ), M( ) or d( ): whole numbers apart by commas, each once.

  function padKarnaughNumbers ( $text ) {

    $text = trim ( (string) $text );

    if ( $text === '' )
      return [];

    $numbers = [];

    foreach ( preg_split ( '/\s*,\s*|\s+/u', $text ) as $one ) {
      if ( ! ctype_digit ( $one ) or strlen ( $one ) > 3 )
        return "a term '" . padNotationShow ( $one ) . "' - the terms are the numbers of the cells, m(0, 2, 5)";
      $numbers [] = (int) $one;
    }

    $numbers = array_values ( array_unique ( $numbers ) );
    sort ( $numbers );

    return $numbers;

  }

  // An expression as a tree: [ 'var', name ], [ 'const', 0|1 ], [ 'not', tree ] and [ op, left,
  // right ] for and, or, xor. Or nots + or | ∨ "or", xor ^ ⊕ "xor", and * · & ∧ "and" or two
  // terms side by side - AB is A and B - not ~ ! ¬ "not" before or ' after; brackets.

  function padKarnaughParse ( $text ) {

    if ( strlen ( $text ) > PAD_KARNAUGH_LONGEST )
      return "an expression of " . strlen ( $text ) . " bytes - an expression is at most " . PAD_KARNAUGH_LONGEST;

    $tokens = padKarnaughTokens ( $text );

    if ( is_string ( $tokens ) )
      return $tokens;

    if ( ! $tokens )
      return "no expression";

    $at   = 0;
    $tree = padKarnaughOr ( $tokens, $at, 0 );

    if ( is_string ( $tree ) )
      return $tree;

    if ( $at < count ( $tokens ) )
      return "an expression it cannot read at '" . padNotationShow ( $tokens [$at] [1] ) . "'";

    return $tree;

  }

  function padKarnaughTokens ( $text ) {

    $tokens = [];
    $chars  = mb_str_split ( $text );
    $count  = count ( $chars );

    for ( $i = 0; $i < $count; $i++ ) {

      $char = $chars [$i];

      if ( ctype_space ( $char ) )
        continue;

      if ( ctype_alpha ( $char ) ) {
        $word = '';
        while ( $i < $count and ctype_alnum ( $chars [$i] ) )
          $word .= $chars [ $i++ ];
        $i--;
        $lower = strtolower ( $word );
        if ( in_array ( $lower, [ 'and', 'or', 'xor', 'not' ] ) )
          $tokens [] = [ $lower, $word ];
        elseif ( preg_match ( '/^(?:[A-Za-z]\d*)+$/D', $word ) ) {
          preg_match_all ( '/[A-Za-z]\d*/', $word, $names );
          foreach ( $names [0] as $one )
            $tokens [] = [ 'var', $one ];
        } else
          return "a name '" . padNotationShow ( $word ) . "' - a variable is a letter, digits after it: A, x1";
        continue;
      }

      if ( $char === '0' or $char === '1' ) {
        $tokens [] = [ 'const', $char ];
        continue;
      }

      $kind = [ '+' => 'or', '|' => 'or', '∨' => 'or', '*' => 'and', '·' => 'and', '⋅' => 'and', '&' => 'and', '∧' => 'and',
                '^' => 'xor', '⊕' => 'xor', '~' => 'not', '!' => 'not', '¬' => 'not', "'" => 'prime', '’' => 'prime', '′' => 'prime',
                '(' => '(', ')' => ')' ] [$char] ?? NULL;

      if ( $kind === NULL )
        return "a sign '" . padNotationShow ( $char ) . "' an expression has not - variables, 0, 1, ' ~ ! + * ^ and brackets, or and, or, xor, not";

      $tokens [] = [ $kind, $char ];

    }

    return $tokens;

  }

  // The levels of the expression, loosest first: or, xor, and, not. A tree nested deeper than
  // 200 brackets is refused, as it would take the stack.

  function padKarnaughOr ( $tokens, &$at, $depth ) {

    if ( $depth > 200 )
      return "an expression nested more than 200 deep";

    $left = padKarnaughXor ( $tokens, $at, $depth );

    while ( ! is_string ( $left ) and ( $tokens [$at] [0] ?? '' ) == 'or' ) {
      $at++;
      $right = padKarnaughXor ( $tokens, $at, $depth );
      if ( is_string ( $right ) )
        return $right;
      $left = [ 'or', $left, $right ];
    }

    return $left;

  }

  function padKarnaughXor ( $tokens, &$at, $depth ) {

    $left = padKarnaughAnd ( $tokens, $at, $depth );

    while ( ! is_string ( $left ) and ( $tokens [$at] [0] ?? '' ) == 'xor' ) {
      $at++;
      $right = padKarnaughAnd ( $tokens, $at, $depth );
      if ( is_string ( $right ) )
        return $right;
      $left = [ 'xor', $left, $right ];
    }

    return $left;

  }

  function padKarnaughAnd ( $tokens, &$at, $depth ) {

    $left = padKarnaughNot ( $tokens, $at, $depth );

    while ( ! is_string ( $left ) and in_array ( $tokens [$at] [0] ?? '', [ 'and', 'var', 'const', 'not', '(' ] ) ) {
      if ( $tokens [$at] [0] == 'and' )
        $at++;
      $right = padKarnaughNot ( $tokens, $at, $depth );
      if ( is_string ( $right ) )
        return $right;
      $left = [ 'and', $left, $right ];
    }

    return $left;

  }

  function padKarnaughNot ( $tokens, &$at, $depth ) {

    $token = $tokens [$at] ?? NULL;

    if ( $token === NULL )
      return "an expression that ends where a variable is due";

    if ( $token [0] == 'not' ) {
      $at++;
      $inner = padKarnaughNot ( $tokens, $at, $depth + 1 );
      return is_string ( $inner ) ? $inner : [ 'not', $inner ];
    }

    if ( $token [0] == 'var' or $token [0] == 'const' ) {
      $at++;
      $tree = ( $token [0] == 'var' ) ? [ 'var', $token [1] ] : [ 'const', (int) $token [1] ];
    } elseif ( $token [0] == '(' ) {
      $at++;
      $tree = padKarnaughOr ( $tokens, $at, $depth + 1 );
      if ( is_string ( $tree ) )
        return $tree;
      if ( ( $tokens [$at] [0] ?? '' ) != ')' )
        return "a bracket that does not close";
      $at++;
    } else
      return "an expression it cannot read at '" . padNotationShow ( $token [1] ) . "'";

    while ( ( $tokens [$at] [0] ?? '' ) == 'prime' ) {
      $at++;
      $tree = [ 'not', $tree ];
    }

    return $tree;

  }

  function padKarnaughUsed ( $tree ) {

    $used  = [];
    $stack = [ $tree ];

    while ( $stack ) {
      $node = array_pop ( $stack );
      if ( $node [0] == 'var' )
        $used [ $node [1] ] = TRUE;
      elseif ( $node [0] == 'not' )
        $stack [] = $node [1];
      elseif ( $node [0] != 'const' )
        array_push ( $stack, $node [1], $node [2] );
    }

    return $used;

  }

  function padKarnaughEval ( $tree, $values ) {

    return match ( $tree [0] ) {
      'var'   => (bool) $values [ $tree [1] ],
      'const' => (bool) $tree [1],
      'not'   => ! padKarnaughEval ( $tree [1], $values ),
      'and'   => padKarnaughEval ( $tree [1], $values ) and padKarnaughEval ( $tree [2], $values ),
      'or'    => padKarnaughEval ( $tree [1], $values ) or padKarnaughEval ( $tree [2], $values ),
      'xor'   => padKarnaughEval ( $tree [1], $values ) xor padKarnaughEval ( $tree [2], $values ) };

  }

  // The cells of a cube [ value, mask ] - the mask the variables it leaves free.

  function padKarnaughCells ( $cube, $count ) {

    list ( $value, $mask ) = $cube;

    $cells = [];

    for ( $index = 0; $index < 2 ** $count; $index++ )
      if ( ( $index & ~$mask ) == $value )
        $cells [] = $index;

    return $cells;

  }

  // The prime implicants of the cells holding $want ('1' for a sum of products, '0' for a
  // product of sums), don't cares free to join: every cube whose cells are all $want or x and
  // that no cube of one more free variable holds - and that holds a cell of $want. In order:
  // the larger first, then by their first cell.

  function padKarnaughPrimes ( $cells, $count, $want ) {

    $full  = 2 ** $count - 1;
    $good  = [];

    for ( $mask = 0; $mask <= $full; $mask++ )
      for ( $value = 0; $value <= $full; $value++ ) {
        if ( $value & $mask )
          continue;
        $ok = TRUE;
        foreach ( padKarnaughCells ( [ $value, $mask ], $count ) as $cell )
          if ( $cells [$cell] !== $want and $cells [$cell] !== 'x' ) {
            $ok = FALSE;
            break;
          }
        if ( $ok )
          $good ["$value:$mask"] = [ $value, $mask ];
      }

    $primes = [];

    foreach ( $good as list ( $value, $mask ) ) {
      $prime = TRUE;
      for ( $bit = 1; $bit <= $full; $bit <<= 1 )
        if ( ! ( $mask & $bit ) and isset ( $good [ ( $value & ~$bit ) . ':' . ( $mask | $bit ) ] ) )
          $prime = FALSE;
      $covers = array_values ( array_filter ( padKarnaughCells ( [ $value, $mask ], $count ), fn ( $cell ) => $cells [$cell] === $want ) );
      if ( $prime and $covers )
        $primes [] = [ $value, $mask, padKarnaughCells ( [ $value, $mask ], $count ), $covers ];
    }

    usort ( $primes, fn ( $a, $b ) => [ count ( $b [2] ), $a [2] ] <=> [ count ( $a [2] ), $b [2] ] );

    return $primes;

  }

  // A minimal cover: the essential primes - the only one over a cell - then of the rest the
  // fewest that cover what is left, of those the fewest literals, of those the first in order.

  function padKarnaughCover ( $primes, $cells, $count, $want ) {

    $left   = array_keys ( array_filter ( $cells, fn ( $one ) => $one === $want ) );
    $chosen = [];

    foreach ( $left as $cell ) {
      $over = array_keys ( array_filter ( $primes, fn ( $prime ) => in_array ( $cell, $prime [3] ) ) );
      if ( count ( $over ) == 1 )
        $chosen [ $over [0] ] = TRUE;
    }

    $covered = [];

    foreach ( array_keys ( $chosen ) as $at )
      foreach ( $primes [$at] [3] as $cell )
        $covered [$cell] = TRUE;

    $left = array_values ( array_filter ( $left, fn ( $cell ) => ! isset ( $covered [$cell] ) ) );
    $rest = array_values ( array_filter ( array_keys ( $primes ), fn ( $at ) => ! isset ( $chosen [$at] ) and array_intersect ( $primes [$at] [3], $left ) ) );

    $literals = fn ( $at ) => $count - substr_count ( decbin ( $primes [$at] [1] ), '1' );

    $best = $left ? NULL : [];

    for ( $size = 1; $left and $best === NULL and $size <= count ( $rest ); $size++ ) {
      $score = PHP_INT_MAX;
      foreach ( padKarnaughCombinations ( count ( $rest ), $size ) as $pick ) {
        $set  = array_map ( fn ( $one ) => $rest [$one], $pick );
        $over = [];
        foreach ( $set as $at )
          foreach ( $primes [$at] [3] as $cell )
            $over [$cell] = TRUE;
        if ( array_diff ( $left, array_keys ( $over ) ) )
          continue;
        $cost = array_sum ( array_map ( $literals, $set ) );
        if ( $cost < $score ) {
          $score = $cost;
          $best  = $set;
        }
      }
    }

    $picked = array_merge ( array_keys ( $chosen ), $best ?? [] );
    sort ( $picked );

    return array_map ( fn ( $at ) => $primes [$at], $picked );

  }

  // Every way to pick $size of $count, in order: [0, 1], [0, 2], ... [1, 2] ...

  function padKarnaughCombinations ( $count, $size ) {

    $pick = range ( 0, $size - 1 );

    while ( TRUE ) {
      yield $pick;
      $i = $size - 1;
      while ( $i >= 0 and $pick [$i] == $count - $size + $i )
        $i--;
      if ( $i < 0 )
        return;
      $pick [$i]++;
      for ( $j = $i + 1; $j < $size; $j++ )
        $pick [$j] = $pick [ $j - 1 ] + 1;
    }

  }

  // The literals of a group: [ variable, TRUE when it stands as itself ] - in a product the
  // variables a group fixes as they are; in a sum, for the zeros, each the other way round.

  function padKarnaughLiterals ( $cube, $vars, $sum ) {

    list ( $value, $mask ) = $cube;

    $count    = count ( $vars );
    $literals = [];

    foreach ( $vars as $at => $var ) {
      $bit = 1 << ( $count - 1 - $at );
      if ( ! ( $mask & $bit ) )
        $literals [] = [ $var, (bool) ( $value & $bit ) != $sum ];
    }

    return $literals;

  }

  // The expression of the groups as AsciiMath - for padMathMl - and in words.

  function padKarnaughAscii ( $name, $groups, $vars, $sum ) {

    $terms = [];

    foreach ( $groups as $group ) {
      $literals = array_map ( fn ( $one ) => $one [1] ? padKarnaughMathName ( $one [0] ) : 'bar(' . padKarnaughMathName ( $one [0] ) . ')',
                              padKarnaughLiterals ( $group, $vars, $sum ) );
      if ( ! $literals )
        $terms [] = $sum ? '0' : '1';
      elseif ( $sum )
        $terms [] = count ( $literals ) > 1 ? '(' . implode ( ' + ', $literals ) . ')' : $literals [0];
      else
        $terms [] = implode ( ' ', $literals );
    }

    if ( ! $terms )
      $terms [] = $sum ? '1' : '0';

    return padKarnaughMathName ( $name ) . ' = ' . implode ( $sum ? ' ' : ' + ', $terms );

  }

  // A variable or a name as the formula writes it: x1 with its digits as a subscript, x_1; a
  // name of more letters - sum, carry, gt - as a text, upright as a function's name stands,
  // never the AsciiMath word it may be: sum is the sign Σ there, gt the sign >, cin c∈.

  function padKarnaughMathName ( $var ) {

    if ( preg_match ( '/^([A-Za-z])(?:_?(\d+))?$/D', $var, $parts ) )
      return isset ( $parts [2] ) ? "$parts[1]_$parts[2]" : $parts [1];

    return '"' . $var . '"';

  }

  function padKarnaughWords ( $name, $groups, $vars, $sum ) {

    $terms = array_map ( fn ( $group ) => padKarnaughTerm ( $group, $vars, $sum, TRUE ), $groups );

    if ( ! $terms )
      $terms [] = $sum ? '1' : '0';

    return "$name is " . implode ( $sum ? ' and ' : ', or ', $terms );

  }

  // One group in words: A and not B, or (A or not B) in a product of sums.

  function padKarnaughTerm ( $group, $vars, $sum, $brackets ) {

    $literals = array_map ( fn ( $one ) => ( $one [1] ? '' : 'not ' ) . $one [0], padKarnaughLiterals ( $group, $vars, $sum ) );

    if ( ! $literals )
      return $sum ? '0' : '1';

    if ( ! $sum )
      return implode ( ' and ', $literals );

    return ( $brackets and count ( $literals ) > 1 ) ? '(' . implode ( ' or ', $literals ) . ')' : implode ( ' or ', $literals );

  }

  // The value of a group at every cell checked against the map: what the expression says is
  // what the cells hold, don't cares aside.

  function padKarnaughCheck ( $groups, $cells, $count, $sum ) {

    foreach ( $cells as $index => $value ) {
      if ( $value === 'x' )
        continue;
      $in = FALSE;
      foreach ( $groups as $group )
        if ( ( $index & ~$group [1] ) == $group [0] )
          $in = TRUE;
      if ( ( $sum ? ! $in : $in ) != ( $value === '1' ) )
        return FALSE;
    }

    return TRUE;

  }

  // The map. Cells 40 square; the variables of the rows - A, or A and B - down the left, those
  // of the columns across the top, each in Gray-code order; a group a rounded rectangle in its
  // colour, inset a little more for every group so loops over one cell stay apart, a group
  // wrapping round an edge drawn open past it.

  function padKarnaughSvg ( $vars, $cells, $groups, $numbers, $sum, $id, $title, $desc ) {

    $count  = count ( $vars );
    $across = $count - intdiv ( $count, 2 );
    $down   = intdiv ( $count, 2 );
    $cols   = PAD_KARNAUGH_GRAY [$across];
    $rows   = PAD_KARNAUGH_GRAY [$down];
    $size   = 40;
    $left   = 56;
    $top    = 48;
    $width  = $left + count ( $cols ) * $size + 10;
    $height = $top + count ( $rows ) * $size + 10;
    $num    = 'padNotationNum';
    $code   = fn ( $bits, $length ) => str_pad ( decbin ( $bits ), $length, '0', STR_PAD_LEFT );

    $svg  = '<path class="pkm-line" d="M4 4L' . $left . ' ' . $top . '"/>'
          . '<text class="pkm-head" x="6" y="' . ( $top - 4 ) . '">' . padNotationAttr ( padKarnaughJoin ( array_slice ( $vars, 0, $down ) ) ) . '</text>'
          . '<text class="pkm-head" x="' . ( $left - 4 ) . '" y="14" text-anchor="end">' . padNotationAttr ( padKarnaughJoin ( array_slice ( $vars, $down ) ) ) . '</text>';

    foreach ( $cols as $c => $bits )
      $svg .= '<text class="pkm-head" x="' . ( $left + $c * $size + $size / 2 ) . '" y="' . ( $top - 8 ) . '" text-anchor="middle">' . $code ( $bits, $across ) . '</text>';

    foreach ( $rows as $r => $bits )
      $svg .= '<text class="pkm-head" x="' . ( $left - 8 ) . '" y="' . ( $top + $r * $size + $size / 2 + 4 ) . '" text-anchor="end">' . $code ( $bits, $down ) . '</text>';

    foreach ( $rows as $r => $high )
      foreach ( $cols as $c => $low ) {
        $index = ( $high << $across ) | $low;
        $value = $cells [$index];
        $x     = $left + $c * $size;
        $y     = $top + $r * $size;
        $class = ( $value === 'x' ) ? 'pkm-x' : ( ( $value === ( $sum ? '0' : '1' ) ) ? 'pkm-on' : 'pkm-off' );
        $svg  .= '<rect class="pkm-cell" x="' . $x . '" y="' . $y . '" width="' . $size . '" height="' . $size . '"/>'
               . '<text class="' . $class . '" x="' . ( $x + $size / 2 ) . '" y="' . ( $y + $size / 2 + 6 ) . '" text-anchor="middle">' . ( $value === 'x' ? '×' : $value ) . '</text>';
        if ( $numbers )
          $svg .= '<text class="pkm-index" x="' . ( $x + 3 ) . '" y="' . ( $y + 10 ) . '">' . $index . '</text>';
      }

    // The groups, clipped to the map: a run of rows or columns that wraps is two pieces, each
    // reaching past its edge.

    $svg .= '<clipPath id="' . $id . '-clip"><rect x="' . $left . '" y="' . $top . '" width="' . ( count ( $cols ) * $size ) . '" height="' . ( count ( $rows ) * $size ) . '"/></clipPath>'
          . '<g clip-path="url(#' . $id . '-clip)">';

    foreach ( $groups as $g => $group ) {
      $inset  = 3 + ( $g % 3 ) * 3;
      $colRun = padKarnaughRuns ( $cols, $group, $across, 0 );
      $rowRun = padKarnaughRuns ( $rows, $group, $down, $across );
      foreach ( $rowRun as list ( $r1, $r2, $rOpen1, $rOpen2 ) )
        foreach ( $colRun as list ( $c1, $c2, $cOpen1, $cOpen2 ) ) {
          $x1 = $left + $c1 * $size + ( $cOpen1 ? -$size / 2 : $inset );
          $x2 = $left + ( $c2 + 1 ) * $size + ( $cOpen2 ? $size / 2 : -$inset );
          $y1 = $top + $r1 * $size + ( $rOpen1 ? -$size / 2 : $inset );
          $y2 = $top + ( $r2 + 1 ) * $size + ( $rOpen2 ? $size / 2 : -$inset );
          $svg .= '<rect class="pkm-group pkm-g' . ( $g % 6 + 1 ) . '" x="' . $num ( $x1 ) . '" y="' . $num ( $y1 ) . '" width="' . $num ( $x2 - $x1 ) . '" height="' . $num ( $y2 - $y1 ) . '" rx="10"/>';
        }
    }

    $svg .= '</g>';

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pkm-map" role="img" aria-labelledby="' . "$id-title $id-desc" . '"'
         . ' width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
         . "<title id=\"$id-title\">" . padNotationAttr ( $title ) . '</title>'
         . "<desc id=\"$id-desc\">" . padNotationAttr ( $desc ) . '</desc>'
         . $svg . '</svg>';

  }

  // The positions a group takes along one side - the columns (its low bits) or the rows - as
  // runs [ first, last, open before, open after ]: one run, or two when it wraps round the
  // edge, each open on the side where it goes on over the edge.

  function padKarnaughRuns ( $order, $group, $bits, $shift ) {

    list ( $value, $mask ) = $group;

    $all   = ( 1 << $bits ) - 1;
    $fixed = ~( $mask >> $shift ) & $all;
    $want  = ( $value >> $shift ) & $all;
    $in    = [];

    foreach ( $order as $at => $code )
      if ( ( $code & $fixed ) == ( $want & $fixed ) )
        $in [] = $at;

    $last = count ( $order ) - 1;

    if ( count ( $in ) == count ( $order ) )
      return [ [ 0, $last, FALSE, FALSE ] ];

    if ( in_array ( 0, $in ) and in_array ( $last, $in ) and count ( $in ) < count ( $order ) ) {
      $head = 0;
      while ( in_array ( $head + 1, $in ) )
        $head++;
      $tail = $last;
      while ( in_array ( $tail - 1, $in ) )
        $tail--;
      return [ [ 0, $head, TRUE, FALSE ], [ $tail, $last, FALSE, TRUE ] ];
    }

    return [ [ min ( $in ), max ( $in ), FALSE, FALSE ] ];

  }

  // The variables of a side of the map as its corner names them: AB, or x1 x2.

  function padKarnaughJoin ( $vars ) {

    return implode ( array_filter ( $vars, fn ( $var ) => strlen ( $var ) > 1 ) ? ' ' : '', $vars );

  }

  // The map in words: its rows, then its groups.

  function padKarnaughDescribe ( $name, $vars, $cells, $groups, $sum ) {

    $count  = count ( $vars );
    $across = $count - intdiv ( $count, 2 );
    $down   = intdiv ( $count, 2 );
    $code   = fn ( $bits, $length ) => str_pad ( decbin ( $bits ), $length, '0', STR_PAD_LEFT );
    $said   = [];

    $said [] = "Rows by " . padKarnaughJoin ( array_slice ( $vars, 0, $down ) ) . ': '
             . implode ( ', ', array_map ( fn ( $bits ) => $code ( $bits, $down ), PAD_KARNAUGH_GRAY [$down] ) )
             . '; columns by ' . padKarnaughJoin ( array_slice ( $vars, $down ) ) . ': '
             . implode ( ', ', array_map ( fn ( $bits ) => $code ( $bits, $across ), PAD_KARNAUGH_GRAY [$across] ) ) . '.';

    foreach ( PAD_KARNAUGH_GRAY [$down] as $high )
      $said [] = 'Row ' . $code ( $high, $down ) . ': '
               . implode ( ', ', array_map ( fn ( $low ) => ( $cells [ ( $high << $across ) | $low ] === 'x' ) ? "don't care" : $cells [ ( $high << $across ) | $low ], PAD_KARNAUGH_GRAY [$across] ) ) . '.';

    foreach ( $groups as $group )
      $said [] = 'Group ' . padKarnaughTerm ( $group, $vars, $sum, FALSE ) . ( count ( $group [2] ) == 1 ? ': cell ' : ': cells ' ) . implode ( ', ', $group [2] ) . '.';

    return implode ( ' ', $said );

  }

?>
