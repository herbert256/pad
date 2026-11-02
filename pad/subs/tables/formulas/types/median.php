<?php

  // median - MEDIAN(number, ...) is the middle number - of an even count the mean of the two in
  // the middle; without any number #NUM!.
  //
  //   =MEDIAN(3, 1, 2)              2
  //   =MEDIAN(B2:B7)                9

  return [
    'group' => 'Statistics',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $numbers = padFormulaNumbers ( $args );
      if ( padFormulaIsError ( $numbers ) )
        return $numbers;
      if ( ! $numbers )
        return padFormulaError ( '#NUM!' );
      sort ( $numbers );
      $count = count ( $numbers );
      $half  = intdiv ( $count, 2 );
      return $count % 2 ? $numbers [$half] : ( $numbers [ $half - 1 ] + $numbers [$half] ) / 2;
    }
  ];

?>
