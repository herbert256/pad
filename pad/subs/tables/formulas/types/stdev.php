<?php

  // stdev - STDEV(number, ...) is the standard deviation of a sample - divided by the count less
  // one; of fewer than two numbers #DIV/0!.
  //
  //   =STDEV(2, 4, 4, 4, 5, 5, 7, 9)   2.138089935
  //   =STDEV(5)                        #DIV/0!

  return [
    'group' => 'Statistics',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $stats = padFormulaStatistics ( $args );
      if ( padFormulaIsError ( $stats ) )
        return $stats;
      return $stats ['count'] < 2 ? padFormulaError ( '#DIV/0!' ) : sqrt ( $stats ['squares'] / ( $stats ['count'] - 1 ) );
    }
  ];

?>
