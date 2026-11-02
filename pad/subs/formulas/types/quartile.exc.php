<?php

  // quartile.exc - QUARTILE.EXC(array, quart) is a quartile of the numbers, as PERCENTILE.EXC finds
  // it at quart/4: 1 the first quartile, 2 the median, 3 the third. The quart is cut to a whole
  // number toward 0; 0 or below, 4 or above, too few numbers to reach the quartile's place, or
  // none, is #NUM!.
  //
  //   =QUARTILE.EXC(B2:B7, 1)         3.75
  //   =QUARTILE.EXC(B2:B7, 3)         16.25
  //   =QUARTILE.EXC(B2:B7, 4)         #NUM!

  return [
    'group' => 'Statistics',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $numbers = padFormulaNumbers ( [ $args [0] ] );
      $quart   = padFormulaArgInt ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $numbers, $quart ) )
        return $error;
      return ( $quart < 1 or $quart > 3 ) ? padFormulaError ( '#NUM!' ) : padFormulaPercentile ( $numbers, $quart / 4, TRUE );
    }
  ];

?>
