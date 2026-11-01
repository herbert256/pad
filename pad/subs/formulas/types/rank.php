<?php

  // rank - RANK(number, ref, [order]) is the place of a number among the numbers of a range: from
  // the largest down, or with an order other than 0 from the smallest up; equal numbers share a
  // place. A number the range does not hold is #N/A.
  //
  //   =RANK(15, B2:B7)              2
  //   =RANK(15, B2:B7, 1)           5

  return [
    'group' => 'Statistics',
    'args'  => [ 2, 3 ],
    'call'  => function ( $args, $env ) {
      $number = padFormulaArgNumber ( $args [0], $env );
      if ( padFormulaIsError ( $number ) )
        return $number;
      if ( ! padFormulaIsRange ( $args [1] ) )
        return padFormulaError ( '#VALUE!' );
      $order = isset ( $args [2] ) ? padFormulaArgNumber ( $args [2], $env ) : 0.0;
      if ( padFormulaIsError ( $order ) )
        return $order;
      $numbers = padFormulaNumbers ( [ $args [1] ] );
      if ( padFormulaIsError ( $numbers ) )
        return $numbers;
      $wanted = padFormulaRound15 ( $number );
      $place  = 1;
      $found  = FALSE;
      foreach ( $numbers as $one ) {
        $one = padFormulaRound15 ( $one );
        if ( $one == $wanted )
          $found = TRUE;
        elseif ( $order == 0 ? $one > $wanted : $one < $wanted )
          $place++;
      }
      return $found ? (float) $place : padFormulaError ( '#N/A' );
    }
  ];

?>
