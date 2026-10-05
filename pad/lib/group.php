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

      $text = strtolower ( trim ( (string) $value ) );
      if ( str_contains ( $text, '.' ) and ! str_contains ( $text, 'e' ) )
        $decimals = max ( $decimals, strlen ( $text ) - strpos ( $text, '.' ) - 1 );

    }

    if ( is_float ( $sum ) )
      $sum = round ( $sum, $decimals );

    if ( $fun == 'sum' )
      return $sum;

    return $numbers ? $sum / $numbers : '';

  }

?>
