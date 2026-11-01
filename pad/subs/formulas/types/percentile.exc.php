<?php

  // percentile.exc - PERCENTILE.EXC(array, k) is the k-th percentile of the numbers - k above 0 and
  // below 1, both left out. Between two numbers it interpolates: the numbers sorted, the
  // percentile stands at the place k*(n+1) among them, and a place before the first number or
  // past the last is #NUM!, as is a k of 0 or 1 or past them, or no number at all; a k that is
  // no number is #VALUE!.
  //
  //   =PERCENTILE.EXC(B2:B7, 0.25)    3.75
  //   =PERCENTILE.EXC(B2:B7, 0.5)     9
  //   =PERCENTILE.EXC(B2:B7, 0.1)     #NUM!

  return [
    'group' => 'Statistics',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $numbers = padFormulaNumbers ( [ $args [0] ] );
      $k       = padFormulaArgNumber ( $args [1], $env );
      return padFormulaFirstError ( $numbers, $k ) ?? padFormulaPercentile ( $numbers, $k, TRUE );
    }
  ];

?>
