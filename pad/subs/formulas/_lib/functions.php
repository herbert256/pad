<?php

  // What several functions of types/ share.
  //
  //   padFormulaAt         the value at a place of a range, counted from its first cell -
  //                        read from the sheet, so past the range's own size too, which
  //                        SUMIF's sum range takes as Excel does: its first cell and the
  //                        size of the criteria's range
  //   padFormulaHits       the places of ranges where every criterion holds - SUMIFS,
  //                        COUNTIFS - and how many empty places past the sheet do
  //   padFormulaIfsValues  the cells of a range where the criteria of other ranges hold -
  //                        AVERAGEIFS, MINIFS, MAXIFS
  //   padFormulaLookup     where a value is found in a list: exactly, or the largest not
  //                        above it in a list that rises (the smallest not below it in one
  //                        that falls), as VLOOKUP, HLOOKUP and MATCH find it
  //   padFormulaAnnuity    what a loan or a saving comes to: the equation PV, FV, PMT, NPER
  //                        and RATE each solve for another of its terms
  //   padFormulaNewton     a root by Newton's method from a guess, as RATE and IRR find
  //                        theirs: 150 steps at most, #NUM! when they do not settle
  //   padFormulaStatistics the numbers of a list of arguments with their count, mean and
  //                        sum of squares
  //   padFormulaMultiple   a number as a whole multiple of another, up, down or the nearest -
  //                        CEILING, FLOOR, MROUND

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

  // Of the arguments of AVERAGEIFS, MINIFS and MAXIFS - a range, then pairs of a range and its
  // criteria - the values of the first range at the places where every criterion holds, as a
  // list: every range the size and shape of the first, else #VALUE!, and an error at one of
  // those places the answer. The empty places past the sheet hold no value to add.

  function padFormulaIfsValues ( $args, $env ) {

    $target = $args [0];

    if ( ! padFormulaIsRange ( $target ) )
      return padFormulaError ( '#VALUE!' );

    $pairs = [];

    for ( $at = 1; $at + 1 < count ( $args ); $at += 2 ) {
      if ( padFormulaIsRange ( $args [$at] ) and ( $args [$at] ['w'] != $target ['w'] or $args [$at] ['h'] != $target ['h'] ) )
        return padFormulaError ( '#VALUE!' );
      $pairs [] = [ $args [$at], $args [$at + 1] ];
    }

    $hits = padFormulaHits ( $pairs, $env );

    if ( padFormulaIsError ( $hits ) )
      return $hits;

    $values = [];

    foreach ( $hits [0] as [ $row, $column ] ) {
      $value = padFormulaAt ( $target, $row, $column, $env );
      if ( padFormulaIsError ( $value ) )
        return $value;
      $values [] = $value;
    }

    return $values;

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

    [ $grow, $sum ] = padFormulaGrowth ( $rate, $nper );

    return $pv * $grow + $pmt * ( 1 + $rate * $type ) * $sum + $fv;

  }

  // The terms of every annuity: what a rate grows to over nper periods, (1+rate)^nper, and
  // the sum of what it grew to in the periods before, ((1+rate)^nper - 1) / rate - nper at a
  // rate of 0. Both are worked out from log1p, with exp and expm1, where 1+rate is above 0:
  // a rate too small for 1+rate to differ from 1 - 1E-17 - still counts as the rate it is,
  // where (1+rate)^nper - 1 was 0, PMT divided by it and the request ended; and 1+rate is
  // never rounded before it is raised to the power, which cost the last two of the 15
  // digits of a payment over 300 months. Below -1 the power of a negative number is all
  // there is - (1-200%)^2 is 1, and a fraction of a period NAN, which is #NUM!.

  function padFormulaGrowth ( $rate, $nper ) {

    if ( $rate == 0 )
      return [ 1.0, (float) $nper ];

    if ( $rate > -1 ) {
      $log = $nper * log1p ( $rate );
      return [ exp ( $log ), expm1 ( $log ) / $rate ];
    }

    // 0 to a negative power - a rate of -100% over periods before now - is INF, and #NUM!:
    // PHP deprecates the power, and PAD reports the deprecation as an error.

    if ( 1 + $rate == 0 )
      $grow = $nper > 0 ? 0.0 : ( $nper == 0 ? 1.0 : INF );
    else
      $grow = (float) ( ( 1 + $rate ) ** $nper );

    return [ $grow, ( $grow - 1 ) / $rate ];

  }

  // Newton's method from a guess, to a step below 0.0000001, as Excel settles RATE and IRR.
  // Excel's help says 20 steps, yet it answers the rate of a thirty-year mortgage paid
  // monthly from no guess: its own iteration settles sooner than Newton on the annuity
  // equation, which from 10% moves about 1/nper of the way each step - 22 steps for 180
  // months, 38 for 360, 49 for 480 - and each of those was #NUM! here. 150 steps, as
  // LibreOffice takes for RATE, settle a loan of 100 years paid monthly. $function answers the
  // value at a rate, or [ value, slope ] where it works the slope out itself; else the slope
  // is taken from two values beside the rate.

  function padFormulaNewton ( $function, $guess ) {

    $rate = $guess;

    for ( $step = 0; $step < 150; $step++ ) {

      [ $value, $slope ] = padFormulaNewtonStep ( $function, $rate );

      if ( ! is_finite ( $value ) or ! is_finite ( $slope ) or $slope == 0 )
        return padFormulaError ( '#NUM!' );

      $next = $rate - $value / $slope;

      if ( ! is_finite ( $next ) or $next <= -1 )
        return padFormulaError ( '#NUM!' );

      if ( abs ( $next - $rate ) < 1e-7 ) {

        // Settled as far as Excel asks; a few steps more give the last digits.

        for ( $more = 0; $more < 4 and abs ( $next - $rate ) > 1e-15; $more++ ) {
          $rate = $next;
          [ $value, $slope ] = padFormulaNewtonStep ( $function, $rate );
          if ( $slope == 0 or ! is_finite ( $value ) or ! is_finite ( $slope ) )
            break;
          $next = $rate - $value / $slope;
        }

        return is_finite ( $next ) ? $next : padFormulaError ( '#NUM!' );

      }

      $rate = $next;

    }

    return padFormulaError ( '#NUM!' );

  }

  // The value of a Newton's function at a rate and its slope there.

  function padFormulaNewtonStep ( $function, $rate ) {

    $value = $function ( $rate );

    if ( is_array ( $value ) )
      return $value;

    $delta = 1e-6 * max ( 1, abs ( $rate ) );

    return [ $value, ( $function ( $rate + $delta ) - $function ( $rate - $delta ) ) / ( 2 * $delta ) ];

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

  // A number as a whole multiple of another, neither 0: the quotient on its 15 digits - 2.5 /
  // 0.1 is 25.000000000000004 as floats, and a number that is a multiple as it shows stays
  // itself - then rounded up toward plus infinity ('up'), down toward minus infinity ('down')
  // or to the nearest, half away from zero ('half'), and times the multiple on 15 digits again
  // - 3 * 0.1 is 0.30000000000000004. -0.25 rounded up to a whole 1 is 0, never -0.

  function padFormulaMultiple ( $number, $multiple, $mode ) {

    $quotient = padFormulaRound15 ( $number / $multiple );

    $whole = match ( $mode ) {
      'up'    => ceil ( $quotient ),
      'down'  => floor ( $quotient ),
      default => round ( $quotient )
    };

    $result = padFormulaRound15 ( $whole * $multiple );

    return $result == 0 ? 0.0 : $result;

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

    return padFormulaIsError ( $number ) ? $number : padFormulaInt ( $number < 0 ? ceil ( padFormulaRound15 ( $number ) ) : floor ( padFormulaRound15 ( $number ) ) );

  }

  // A whole number as an int, 2^53 at most either way - past it no float holds every whole
  // number - so a count past the end of whatever a function counts is one, and the function
  // answers what it answers for such a count: LEFT("abc",1E300) the text, CHOOSE #VALUE!,
  // INDEX #REF!, DATE and LARGE #NUM!. Cast as it was, 1E300 is no int, and PHP's warning
  // ended the request.

  const padFormulaMaxWhole = 9007199254740992;

  function padFormulaInt ( $number ) {

    return (int) max ( -padFormulaMaxWhole, min ( padFormulaMaxWhole, $number ) );

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
