<?php

  // trunc - TRUNC(number, [digits]) cuts a number to a number of decimals - none when digits is
  // left out - toward zero, so -8.9 is -8 where INT makes it -9; digits below 0 cut to tens,
  // hundreds. It cuts the number as it shows, to its 15 digits, as ROUNDDOWN does.
  //
  //   =TRUNC(8.9)                   8
  //   =TRUNC(-8.9)                  -8
  //   =TRUNC(3.14159, 2)            3.14
  //   =TRUNC(1234.567, -2)          1200

  return [
    'group' => 'Math',
    'args'  => [ 1, 2 ],
    'call'  => function ( $args, $env ) {
      $number = padFormulaArgNumber ( $args [0], $env );
      $digits = array_key_exists ( 1, $args ) ? padFormulaArgInt ( $args [1], $env ) : 0;
      return padFormulaFirstError ( $number, $digits ) ?? padFormulaRoundTo ( $number, $digits, 'down' );
    }
  ];

?>
