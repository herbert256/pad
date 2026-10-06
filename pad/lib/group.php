<?php

  // The aggregate of one field over the rows of a group, for the group handling option
  // (handling/types/group.php): sum, avg, min or max over the values the rows hold.
  //
  // sum adds the numeric values and leaves out the rest, as SQL leaves out a NULL; its
  // result is rounded to the most decimals among the values added, because float addition
  // turned 10.10 + 20.20 into 30.299999999999997 on a page that showed money. avg divides
  // that sum by the number of numeric values, '' when there were none. min and max compare
  // the values that are there - a missing or empty value is left out - the way PHP compares
  // them: numbers as numbers, other text as text; '' when no value is left.

  function padGroupAggregate ( $fun, $values ) {

    if ( $fun == 'min' or $fun == 'max' ) {

      $values = array_filter ( $values, fn ( $v ) => $v !== NULL and $v !== '' and is_scalar ( $v ) );

      if ( ! $values )
        return '';

      return ( $fun == 'min' ) ? min ( $values ) : max ( $values );

    }

    $sum      = 0;
    $numbers  = 0;
    $decimals = 0;

    foreach ( $values as $value ) {

      if ( ! is_numeric ( $value ) )
        continue;

      $sum += $value + 0;
      $numbers++;

      // A value in E notation - PHP writes every float below 0.0001 that way, 1.0E-5 - has
      // the decimals of its mantissa less its exponent. It was left out of the count, so
      // 0.00001 + 0.5 was rounded to the one decimal of 0.5 and came out 0.5.

      $text = strtolower ( trim ( (string) $value ) );
      $mant = str_contains ( $text, 'e' ) ? strstr ( $text, 'e', TRUE ) : $text;
      $exp  = str_contains ( $text, 'e' ) ? (int) substr ( strstr ( $text, 'e' ), 1 ) : 0;
      $dec  = str_contains ( $mant, '.' ) ? strlen ( $mant ) - strpos ( $mant, '.' ) - 1 : 0;
      $decimals = max ( $decimals, $dec - $exp );

    }

    if ( is_float ( $sum ) )
      $sum = round ( $sum, $decimals );

    if ( $fun == 'sum' )
      return $sum;

    return $numbers ? $sum / $numbers : '';

  }

?>
