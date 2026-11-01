<?php

  // product - PRODUCT(number, ...) multiplies numbers, taken as SUM takes them: of a range only
  // its numbers. Without any number it is 0.
  //
  //   =PRODUCT(2, 3, 4)             24
  //   =PRODUCT(C2:C3)               0.96

  return [
    'group' => 'Math',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $numbers = padFormulaNumbers ( $args );
      if ( padFormulaIsError ( $numbers ) )
        return $numbers;
      return $numbers ? (float) array_product ( $numbers ) : 0.0;
    }
  ];

?>
