<?php

  // var - VAR(number, ...) is the variance of a sample - divided by the count less one; of fewer
  // than two numbers #DIV/0!.
  //
  //   =VAR(2, 4, 4, 4, 5, 5, 7, 9)     4.571428571
  //   =VAR(1, 2, 3, 4)                 1.666666667

  return [
    'group' => 'Statistics',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $stats = padFormulaStatistics ( $args );
      if ( padFormulaIsError ( $stats ) )
        return $stats;
      return $stats ['count'] < 2 ? padFormulaError ( '#DIV/0!' ) : $stats ['squares'] / ( $stats ['count'] - 1 );
    }
  ];

?>
