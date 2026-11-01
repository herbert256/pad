<?php

  // What several functions of types/ share.
  //
  //   padFormulaAt         the value at a place of a range, counted from its first cell -
  //                        read from the sheet, so past the range's own size too, which
  //                        SUMIF's sum range takes as Excel does: its first cell and the
  //                        size of the criteria's range
  //   padFormulaHits       the places of ranges where every criterion holds - SUMIFS,
  //                        COUNTIFS - and how many empty places past the sheet do
  //   padFormulaLookup     where a value is found in a list: exactly, or the largest not
  //                        above it in a list that rises (the smallest not below it in one
  //                        that falls), as VLOOKUP, HLOOKUP and MATCH find it
  //   padFormulaAnnuity    what a loan or a saving comes to: the equation PV, FV, PMT, NPER
  //                        and RATE each solve for another of its terms
  //   padFormulaNewton     a root by Newton's method from a guess, as RATE and IRR find
  //                        theirs: 20 steps at most, #NUM! when they do not settle
  //   padFormulaStatistics the numbers of a list of arguments with their count, mean and
  //                        sum of squares

  function padFormulaAt ( $range, $row, $column, $env ) {

    if ( $row < count ( $range ['range'] ) and $column < count ( $range ['range'] [$row] ?? [] ) )
      return $range ['range'] [$row] [$column];

    $c = $range ['c'] + $column;
    $r = $range ['r'] + $row;

    if ( $c > ( $env ['width'] ?? 0 ) or $r > ( $env ['height'] ?? 0 ) )
      return NULL;

    return $env ['value'] ( $c, $r );

  }

  // The places [ row, column ] of the ranges - all of one size, else #VALUE! - where each
  // range's criterion holds, as far as one of them reaches into the sheet; and the number of
  // places past that, which are empty in every range, when every criterion takes an empty
  // cell.

  function padFormulaHits ( $pairs, $env ) {

    $height = NULL;
    $width  = NULL;
    $tests  = [];
    $rows   = 0;
    $cols   = 0;

    foreach ( $pairs as [ $range, $criteria ] ) {

      if ( ! padFormulaIsRange ( $range ) )
        return padFormulaError ( '#VALUE!' );

      if ( ( $height !== NULL and $range ['h'] != $height ) or ( $width !== NULL and $range ['w'] != $width ) )
        return padFormulaError ( '#VALUE!' );

      $height  = $range ['h'];
      $width   = $range ['w'];
      $tests[] = [ $range, padFormulaCriteria ( padFormulaScalar ( $criteria, $env ) ) ];
      $rows    = max ( $rows, min ( $height, ( $env ['height'] ?? 0 ) - $range ['r'] + 1 ) );
      $cols    = max ( $cols, min ( $width,  ( $env ['width']  ?? 0 ) - $range ['c'] + 1 ) );

    }

    $hits = [];

    for ( $r = 0; $r < $rows; $r++ )
      for ( $c = 0; $c < $cols; $c++ ) {
        foreach ( $tests as [ $range, $test ] )
          if ( ! $test ( padFormulaAt ( $range, $r, $c, $env ) ) )
            continue 2;
        $hits [] = [ $r, $c ];
      }

    $past = $height * $width - max ( 0, $rows ) * max ( 0, $cols );

    foreach ( $tests as [ $range, $test ] )
      if ( ! $test ( NULL ) )
        $past = 0;

    return [ $hits, $past ];

  }

  // The place - from 0 - of a value in a list, or FALSE. $mode 0: the first equal one, text
  // with * and ? as wildcards; 1: in a list that rises, the last one not above the value;
  // -1: in a list that falls, the last one not below it. Values of another kind are passed
  // over.

  function padFormulaLookup ( $wanted, $list, $mode ) {

    if ( $mode == 0 ) {
      foreach ( $list as $place => $value )
        if ( padFormulaSame ( $wanted, $value, TRUE ) )
          return $place;
      return FALSE;
    }

    $found = FALSE;

    foreach ( $list as $place => $value ) {
      $order = padFormulaLookupOrder ( $value, $wanted );
      if ( $order === NULL )
        continue;
      if ( ( $mode > 0 and $order <= 0 ) or ( $mode < 0 and $order >= 0 ) )
        $found = $place;
      else
        break;
    }

    return $found;

  }

  // The equation of an annuity, which is 0 when the terms agree:
  //
  //   pv * (1+rate)^nper + pmt * (1 + rate*type) * ((1+rate)^nper - 1) / rate + fv
  //
  // and pv + pmt * nper + fv at a rate of 0. type 1 pays at the start of each period.

  function padFormulaAnnuity ( $rate, $nper, $pmt, $pv, $fv, $type ) {

    if ( $rate == 0 )
      return $pv + $pmt * $nper + $fv;

    $grow = ( 1 + $rate ) ** $nper;

    return $pv * $grow + $pmt * ( 1 + $rate * $type ) * ( $grow - 1 ) / $rate + $fv;

  }

  function padFormulaNewton ( $function, $guess ) {

    $rate = $guess;

    for ( $step = 0; $step < 20; $step++ ) {

      $value = $function ( $rate );
      $delta = 1e-6 * max ( 1, abs ( $rate ) );
      $slope = ( $function ( $rate + $delta ) - $function ( $rate - $delta ) ) / ( 2 * $delta );

      if ( ! is_finite ( $value ) or ! is_finite ( $slope ) or $slope == 0 )
        return padFormulaError ( '#NUM!' );

      $next = $rate - $value / $slope;

      if ( ! is_finite ( $next ) or $next <= -1 )
        return padFormulaError ( '#NUM!' );

      if ( abs ( $next - $rate ) < 1e-7 ) {

        // Settled as far as Excel asks; a few steps more give the last digits.

        for ( $more = 0; $more < 4 and abs ( $next - $rate ) > 1e-15; $more++ ) {
          $rate  = $next;
          $value = $function ( $rate );
          $slope = ( $function ( $rate + $delta ) - $function ( $rate - $delta ) ) / ( 2 * $delta );
          if ( $slope == 0 or ! is_finite ( $value ) )
            break;
          $next  = $rate - $value / $slope;
        }

        return is_finite ( $next ) ? $next : padFormulaError ( '#NUM!' );

      }

      $rate = $next;

    }

    return padFormulaError ( '#NUM!' );

  }

  // The arguments of a finance function as numbers, missing ones as their default; the type
  // is 1 for any value but 0, as Excel takes it.

  function padFormulaFinance ( $args, $defaults, $env ) {

    $numbers = [];

    foreach ( $defaults as $place => $default ) {
      $arg = $args [$place] ?? NULL;
      if ( ! array_key_exists ( $place, $args ) or $arg === NULL )
        $numbers [] = $default;
      else {
        $number = padFormulaArgNumber ( $arg, $env );
        if ( padFormulaIsError ( $number ) )
          return $number;
        $numbers [] = $number;
      }
    }

    return $numbers;

  }

  // The numbers of the arguments by the rules of padFormulaNumbers, with their count, their
  // mean and the sum of the squares of their distances from it.

  function padFormulaStatistics ( $args ) {

    $numbers = padFormulaNumbers ( $args );

    if ( padFormulaIsError ( $numbers ) )
      return $numbers;

    $count = count ( $numbers );
    $mean  = $count ? array_sum ( $numbers ) / $count : 0.0;
    $sum   = 0.0;

    foreach ( $numbers as $number )
      $sum += ( $number - $mean ) ** 2;

    return [ 'numbers' => $numbers, 'count' => $count, 'mean' => $mean, 'squares' => $sum ];

  }

  // The logical values of AND, OR and XOR: typed into the call TRUE, FALSE, a number (0 is
  // FALSE) and the texts TRUE and FALSE - another text is #VALUE!; of a range its logical
  // values and numbers, its text and empty cells left out. None at all is #VALUE!.

  function padFormulaLogicals ( $args ) {

    $values = [];

    foreach ( $args as $arg ) {

      if ( padFormulaIsError ( $arg ) )
        return $arg;

      if ( padFormulaIsRange ( $arg ) ) {
        foreach ( padFormulaFlat ( $arg ) as $value ) {
          if ( padFormulaIsError ( $value ) )
            return $value;
          if ( is_bool ( $value ) or is_float ( $value ) or is_int ( $value ) )
            $values [] = (bool) $value;
        }
        continue;
      }

      if ( $arg === NULL )
        continue;

      $value = padFormulaBool ( $arg );

      if ( padFormulaIsError ( $value ) )
        return $value;

      $values [] = $value;

    }

    return $values ? $values : padFormulaError ( '#VALUE!' );

  }

  // A whole number of an argument, cut toward zero, as Excel takes a count, a place or a
  // number of digits.

  function padFormulaArgInt ( $value, $env ) {

    $number = padFormulaArgNumber ( $value, $env );

    return padFormulaIsError ( $number ) ? $number : (int) ( $number < 0 ? ceil ( padFormulaRound15 ( $number ) ) : floor ( padFormulaRound15 ( $number ) ) );

  }

  // A part of a range: rows and columns from a place, as a range again - what INDEX and
  // XLOOKUP answer.

  function padFormulaPart ( $range, $row, $column, $height, $width, $env ) {

    $rows = [];

    for ( $r = 0; $r < $height and $range ['r'] + $row + $r <= ( $env ['height'] ?? 0 ); $r++ ) {
      $cells = [];
      for ( $c = 0; $c < $width and $range ['c'] + $column + $c <= ( $env ['width'] ?? 0 ); $c++ )
        $cells [] = padFormulaAt ( $range, $row + $r, $column + $c, $env );
      $rows [] = $cells;
    }

    return [ 'range' => $rows, 'c' => $range ['c'] + $column, 'r' => $range ['r'] + $row, 'w' => $width, 'h' => $height ];

  }

?>
