<?php

  // round - ROUND(number, digits) rounds to a number of decimals, half away from zero - 2.5 is 3,
  // -2.5 is -3; digits below 0 round to tens, hundreds. It rounds the number as it shows, to its
  // 15 digits: ROUND(1.005, 2) is 1.01.
  //
  //   =ROUND(2.5, 0)                3
  //   =ROUND(-2.5, 0)               -3
  //   =ROUND(1.005, 2)              1.01
  //   =ROUND(1234.567, -2)          1200

  return [
    'group' => 'Math',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $number = padFormulaArgNumber ( $args [0], $env );
      $digits = padFormulaArgInt ( $args [1], $env );
      return padFormulaFirstError ( $number, $digits ) ?? padFormulaRoundTo ( $number, $digits, 'half' );
    }
  ];

?>
