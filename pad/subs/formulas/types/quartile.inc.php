<?php

  // quartile.inc - QUARTILE.INC(array, quart) is a quartile of the numbers, as PERCENTILE.INC finds
  // it at quart/4: 0 the smallest number, 1 the first quartile, 2 the median, 3 the third
  // quartile, 4 the largest. The quart is cut to a whole number toward 0; below 0 or above 4,
  // or without a number, it is #NUM!.
  //
  //   =QUARTILE.INC(B2:B7, 1)         5.75
  //   =QUARTILE.INC(B2:B7, 3)         13.75
  //   =QUARTILE.INC(C2:C7, 2)         1.725
  //   =QUARTILE.INC(B2:B7, 5)         #NUM!

  return [
    'group' => 'Statistics',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $numbers = padFormulaNumbers ( [ $args [0] ] );
      $quart   = padFormulaArgInt ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $numbers, $quart ) )
        return $error;
      return ( $quart < 0 or $quart > 4 ) ? padFormulaError ( '#NUM!' ) : padFormulaPercentile ( $numbers, $quart / 4, FALSE );
    }
  ];

?>
