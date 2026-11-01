<?php

  // large - LARGE(array, k) is the k-th largest number; a k below 1 or past the count of numbers
  // is #NUM!, a k that is no whole number is rounded up.
  //
  //   =LARGE(B2:B7, 2)              15
  //   =LARGE(B2:B7, 7)              #NUM!

  return [
    'group' => 'Statistics',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $numbers = padFormulaNumbers ( [ $args [0] ] );
      $k       = padFormulaArgNumber ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $numbers, $k ) )
        return $error;
      $k = (int) ceil ( padFormulaRound15 ( $k ) );
      if ( $k < 1 or $k > count ( $numbers ) )
        return padFormulaError ( '#NUM!' );
      rsort ( $numbers );
      return $numbers [ $k - 1 ];
    }
  ];

?>
