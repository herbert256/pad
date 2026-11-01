<?php

  // max - MAX(number, ...) is the largest of numbers, taken as SUM takes them; without any 0.
  //
  //   =MAX(4, -2, 7)                7
  //   =MAX(C2:C7)                   4

  return [
    'group' => 'Statistics',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $numbers = padFormulaNumbers ( $args );
      if ( padFormulaIsError ( $numbers ) )
        return $numbers;
      return $numbers ? max ( $numbers ) : 0.0;
    }
  ];

?>
