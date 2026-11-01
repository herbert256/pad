<?php

  // small - SMALL(array, k) is the k-th smallest number; a k below 1 or past the count of numbers
  // is #NUM!, a k that is no whole number is rounded up.
  //
  //   =SMALL(B2:B7, 2)              5
  //   =SMALL(C2:C7, 1)              0.8

  return [
    'group' => 'Statistics',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $numbers = padFormulaNumbers ( [ $args [0] ] );
      $k       = padFormulaArgNumber ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $numbers, $k ) )
        return $error;
      $k = padFormulaInt ( ceil ( padFormulaRound15 ( $k ) ) );
      if ( $k < 1 or $k > count ( $numbers ) )
        return padFormulaError ( '#NUM!' );
      sort ( $numbers );
      return $numbers [ $k - 1 ];
    }
  ];

?>
