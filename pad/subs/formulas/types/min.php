<?php

  // min - MIN(number, ...) is the smallest of numbers, taken as SUM takes them; without any 0.
  //
  //   =MIN(4, -2, 7)                -2
  //   =MIN(B2:B7)                   0

  return [
    'group' => 'Statistics',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $numbers = padFormulaNumbers ( $args );
      if ( padFormulaIsError ( $numbers ) )
        return $numbers;
      return $numbers ? min ( $numbers ) : 0.0;
    }
  ];

?>
