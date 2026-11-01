<?php

  // average - AVERAGE(number, ...) is the mean of numbers, taken as SUM takes them: of a range
  // only its numbers, so an empty cell does not count as 0. Without any number #DIV/0!.
  //
  //   =AVERAGE(2, 4, 9)             5
  //   =AVERAGE(B2:B7)               9.666666667
  //   =AVERAGE(A2:A7)               #DIV/0!

  return [
    'group' => 'Statistics',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $numbers = padFormulaNumbers ( $args );
      if ( padFormulaIsError ( $numbers ) )
        return $numbers;
      return $numbers ? array_sum ( $numbers ) / count ( $numbers ) : padFormulaError ( '#DIV/0!' );
    }
  ];

?>
