<?php

  // var.p - VAR.P(number, ...) is the variance of a whole population - divided by the count, where
  // VAR.S divides by the count less one; of one number 0, of none #DIV/0!. The numbers are taken
  // as SUM takes them: typed into the call a number, TRUE (1), FALSE (0) and a text that reads as
  // a number count, another text is #VALUE!; of a range only its numbers.
  //
  //   =VAR.P(2, 4, 4, 4, 5, 5, 7, 9)     4
  //   =VAR.P(B2:B7)                      42.22222222
  //   =VAR.P(A2:A7)                      #DIV/0!

  return [
    'group' => 'Statistics',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $stats = padFormulaStatistics ( $args );
      if ( padFormulaIsError ( $stats ) )
        return $stats;
      return $stats ['count'] < 1 ? padFormulaError ( '#DIV/0!' ) : $stats ['squares'] / $stats ['count'];
    }
  ];

?>
