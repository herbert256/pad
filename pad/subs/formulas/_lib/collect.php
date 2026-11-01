<?php

  // What the functions of many values share: the numbers of their arguments, the criteria
  // of COUNTIF and its kin, and the comparisons of the lookups.
  //
  //   padFormulaNumbers   the numbers of a list of arguments, by Excel's rule: typed into the
  //                       call a number counts, so do TRUE (1), FALSE (0) and a text that
  //                       reads as a number - another text is #VALUE!; of a range only the
  //                       numbers count - its text, logical values and empty cells are left
  //                       out. An error anywhere is the answer.
  //   padFormulaCriteria  a criterion - 5, ">5", "<>x", "a*", "" - as a test of one value
  //   padFormulaWild      a text with * and ? as a pattern - ~ takes their meaning away
  //   padFormulaSame      whether two values are equal as a lookup sees them: a number to a
  //                       number, a text to a text in any case, TRUE to TRUE
  //   padFormulaVector    the values of a range of one row or one column, as a list
  //   padFormulaPercentile the k-th percentile of numbers, inclusive or exclusive, as
  //                       PERCENTILE.INC and PERCENTILE.EXC interpolate it

  function padFormulaNumbers ( $args, $text = 'error' ) {

    $numbers = [];

    foreach ( $args as $arg ) {

      if ( padFormulaIsError ( $arg ) )
        return $arg;

      if ( padFormulaIsRange ( $arg ) ) {
        foreach ( padFormulaFlat ( $arg ) as $value ) {
          if ( padFormulaIsError ( $value ) )
            return $value;
          if ( is_float ( $value ) or is_int ( $value ) )
            $numbers [] = (float) $value;
        }
        continue;
      }

      if ( is_string ( $arg ) ) {
        $number = padFormulaParseValue ( $arg );
        if ( $number === NULL ) {
          if ( $text == 'skip' )
            continue;
          return padFormulaError ( '#VALUE!' );
        }
        $numbers [] = $number;
        continue;
      }

      $numbers [] = padFormulaNumber ( $arg );

    }

    return $numbers;

  }

  // A criterion as a function of one value. Written as a value - 5, TRUE - it is equality.
  // As a text it may start with = <> < > <= or >=; what follows is a number when it reads as
  // one (a date too), TRUE or FALSE, else a text, which with = or no operator may hold the
  // wildcards * and ?, in any case. A number matches a number and a text that reads as that
  // number; a text a text; "" an empty cell (and an empty text), "<>" any cell that is not
  // empty. <> matches whatever = does not, empty cells among them.

  function padFormulaCriteria ( $criteria ) {

    if ( padFormulaIsError ( $criteria ) )
      return fn ( $value ) => padFormulaIsError ( $value ) and $value ['error'] == $criteria ['error'];

    if ( $criteria === NULL )
      $criteria = 0.0;

    if ( is_bool ( $criteria ) )
      return fn ( $value ) => $value === $criteria;

    if ( is_float ( $criteria ) or is_int ( $criteria ) )
      return fn ( $value ) => padFormulaCriteriaNumber ( $value, '=', (float) $criteria );

    $text     = (string) $criteria;
    $operator = '=';

    if ( preg_match ( '/^(<=|>=|<>|=|<|>)(.*)$/s', $text, $match ) )
      [ , $operator, $text ] = $match;

    if ( $text === '' )
      return match ( $operator ) {
        '='     => fn ( $value ) => $value === NULL or $value === '',
        '<>'    => fn ( $value ) => ! ( $value === NULL or $value === '' ),
        default => fn ( $value ) => FALSE
      };

    $number = padFormulaParseValue ( $text );

    if ( $number !== NULL )
      return fn ( $value ) => padFormulaCriteriaNumber ( $value, $operator, $number );

    $word = strtoupper ( $text );

    if ( $word === 'TRUE' or $word === 'FALSE' ) {
      $bool = ( $word === 'TRUE' );
      return match ( $operator ) {
        '='     => fn ( $value ) => $value === $bool,
        '<>'    => fn ( $value ) => $value !== $bool,
        default => fn ( $value ) => is_bool ( $value ) and padFormulaCompareWith ( (int) $value <=> (int) $bool, $operator )
      };
    }

    if ( $operator == '=' or $operator == '<>' ) {
      $pattern = padFormulaWild ( $text );
      $same    = fn ( $value ) => is_string ( $value ) and preg_match ( $pattern, $value );
      return $operator == '=' ? $same : fn ( $value ) => ! $same ( $value );
    }

    return fn ( $value ) => is_string ( $value ) and padFormulaCompareWith ( padFormulaTextOrder ( $value, $text ), $operator );

  }

  function padFormulaCriteriaNumber ( $value, $operator, $number ) {

    if ( is_string ( $value ) and $operator == '=' )
      $value = padFormulaParseValue ( $value );
    elseif ( is_string ( $value ) and $operator == '<>' )
      return padFormulaParseValue ( $value ) === NULL or padFormulaParseValue ( $value ) != $number;

    if ( ! is_float ( $value ) and ! is_int ( $value ) )
      return $operator == '<>';

    return padFormulaCompareWith ( padFormulaRound15 ( (float) $value ) <=> padFormulaRound15 ( $number ), $operator );

  }

  function padFormulaCompareWith ( $order, $operator ) {

    return match ( $operator ) {
      '='  => $order == 0,
      '<>' => $order != 0,
      '<'  => $order < 0,
      '>'  => $order > 0,
      '<=' => $order <= 0,
      '>=' => $order >= 0
    };

  }

  // * any run of characters, ? one, ~* ~? ~~ the character itself; in any case.

  function padFormulaWild ( $text ) {

    $pattern = '';
    $length  = mb_strlen ( $text );

    for ( $at = 0; $at < $length; $at++ ) {
      $char = mb_substr ( $text, $at, 1 );
      if ( $char == '~' and $at + 1 < $length and in_array ( mb_substr ( $text, $at + 1, 1 ), [ '*', '?', '~' ], TRUE ) )
        $pattern .= preg_quote ( mb_substr ( $text, ++$at, 1 ), '/' );
      elseif ( $char == '*' )
        $pattern .= '.*';
      elseif ( $char == '?' )
        $pattern .= '.';
      else
        $pattern .= preg_quote ( $char, '/' );
    }

    return '/^' . $pattern . '$/isu';

  }

  function padFormulaHasWild ( $text ) {

    return is_string ( $text ) and preg_match ( '/(?<!~)[*?]/', $text );

  }

  // Equality of a lookup: the same kind and the same value - a text in any case, with * and
  // ? as wildcards when $wild.

  function padFormulaSame ( $wanted, $value, $wild = FALSE ) {

    if ( is_string ( $wanted ) )
      return is_string ( $value ) and ( ( $wild and padFormulaHasWild ( $wanted ) )
                                        ? (bool) preg_match ( padFormulaWild ( $wanted ), $value )
                                        : padFormulaTextOrder ( $wanted, $value ) == 0 );

    if ( is_bool ( $wanted ) )
      return $value === $wanted;

    return ( is_float ( $value ) or is_int ( $value ) ) and padFormulaRound15 ( (float) $value ) == padFormulaRound15 ( (float) $wanted );

  }

  // How a value orders against the one looked for, for an approximate lookup - only values of
  // the same kind compare, NULL for another kind.

  function padFormulaLookupOrder ( $value, $wanted ) {

    $kind = fn ( $one ) => is_bool ( $one ) ? 'b' : ( is_string ( $one ) ? 't' : ( ( is_float ( $one ) or is_int ( $one ) ) ? 'n' : '' ) );

    if ( $kind ( $value ) === '' or $kind ( $value ) !== $kind ( $wanted ) )
      return NULL;

    return padFormulaOrder ( $value, $wanted );

  }

  // The cells of a range of one row or one column as a list - past the sheet's cells, empty
  // ones up to the range's size - or FALSE for a range of more rows and columns. A value that
  // is no range is a list of one.

  function padFormulaVector ( $value ) {

    if ( ! padFormulaIsRange ( $value ) )
      return [ $value ];

    if ( $value ['h'] == 1 ) {
      $list = $value ['range'] [0] ?? [];
      return array_pad ( $list, min ( $value ['w'], max ( count ( $list ), 1 ) ), NULL );
    }

    if ( $value ['w'] == 1 ) {
      $list = array_map ( fn ( $row ) => $row [0] ?? NULL, $value ['range'] );
      return array_pad ( $list, min ( $value ['h'], max ( count ( $list ), 1 ) ), NULL );
    }

    return FALSE;

  }

  // Of a criteria function, the cells of a range that match - [ row, column ] each - and how
  // many of its empty cells past the sheet do (all or none).

  function padFormulaMatching ( $range, $test ) {

    $hits = [];
    $seen = 0;

    if ( ! padFormulaIsRange ( $range ) )
      return [ $test ( $range ) ? [ [ 0, 0 ] ] : [], 0 ];

    foreach ( $range ['range'] as $r => $row )
      foreach ( $row as $c => $value ) {
        $seen++;
        if ( $test ( $value ) )
          $hits [] = [ $r, $c ];
      }

    $past = padFormulaRangeSize ( $range ) - $seen;

    return [ $hits, ( $past > 0 and $test ( NULL ) ) ? $past : 0 ];

  }

  // The k-th percentile of numbers - k a fraction - at its rank among them sorted, from 1:
  // inclusive k(n-1)+1, k from 0 to 1, so 0 is the smallest and 1 the largest; exclusive
  // k(n+1), k above 0 and below 1, and a rank that falls before the first number or past the
  // last has nothing to interpolate between. A rank between two numbers takes the part of
  // the way between them it stands at - worked out on its 15 digits, so 0.3*3+1 is 1.9. No
  // numbers, or a k or rank out of reach, is #NUM!.

  function padFormulaPercentile ( $numbers, $k, $exclusive ) {

    $count = count ( $numbers );

    if ( ! $count )
      return padFormulaError ( '#NUM!' );

    if ( $exclusive ? ( $k <= 0 or $k >= 1 ) : ( $k < 0 or $k > 1 ) )
      return padFormulaError ( '#NUM!' );

    $rank = padFormulaRound15 ( $exclusive ? $k * ( $count + 1 ) : $k * ( $count - 1 ) + 1 );

    if ( $rank < 1 or $rank > $count )
      return padFormulaError ( '#NUM!' );

    sort ( $numbers );

    $low  = (int) floor ( $rank );
    $part = $rank - $low;

    if ( $part == 0 or $low >= $count )
      return $numbers [ $low - 1 ];

    return $numbers [ $low - 1 ] + $part * ( $numbers [$low] - $numbers [ $low - 1 ] );

  }

?>
