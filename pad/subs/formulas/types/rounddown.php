<?php

  // rounddown - ROUNDDOWN(number, digits) rounds toward zero to a number of decimals - cuts the
  // digits off.
  //
  //   =ROUNDDOWN(3.9, 0)            3
  //   =ROUNDDOWN(-3.9, 0)           -3
  //   =ROUNDDOWN(3.14159, 3)        3.141

  return [
    'group' => 'Math',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $number = padFormulaArgNumber ( $args [0], $env );
      $digits = padFormulaArgInt ( $args [1], $env );
      return padFormulaFirstError ( $number, $digits ) ?? padFormulaRoundTo ( $number, $digits, 'down' );
    }
  ];

?>
