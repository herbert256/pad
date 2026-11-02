<?php

  // percentile.inc - PERCENTILE.INC(array, k) is the k-th percentile of the numbers - k from 0 to
  // 1, both included, so 0 is the smallest number and 1 the largest. Between two numbers it
  // interpolates: the numbers sorted, the percentile stands at the place k*(n-1)+1 among them.
  // Only the numbers of a range count; without any, or with a k below 0 or above 1, it is
  // #NUM!, and a k that is no number #VALUE!.
  //
  //   =PERCENTILE.INC(B2:B7, 0.3)     6.5
  //   =PERCENTILE.INC(B2:B7, 0.9)     17.5
  //   =PERCENTILE.INC(B2:B7, 1)       20
  //   =PERCENTILE.INC(B2:B7, 1.5)     #NUM!

  return [
    'group' => 'Statistics',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $numbers = padFormulaNumbers ( [ $args [0] ] );
      $k       = padFormulaArgNumber ( $args [1], $env );
      return padFormulaFirstError ( $numbers, $k ) ?? padFormulaPercentile ( $numbers, $k, FALSE );
    }
  ];

?>
