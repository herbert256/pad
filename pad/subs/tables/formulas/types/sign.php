<?php

  // sign - SIGN(number) is 1 for a number above 0, 0 for 0 and -1 for a number below 0.
  //
  //   =SIGN(10)                     1
  //   =SIGN(4-4)                    0
  //   =SIGN(-0.00001)               -1
  //   =SIGN(E2)                     -1

  return [
    'group' => 'Math',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $number = padFormulaArgNumber ( $args [0], $env );
      return padFormulaIsError ( $number ) ? $number : (float) ( $number <=> 0 );
    }
  ];

?>
