<?php

  // roundup - ROUNDUP(number, digits) rounds away from zero to a number of decimals.
  //
  //   =ROUNDUP(3.2, 0)              4
  //   =ROUNDUP(-3.2, 0)             -4
  //   =ROUNDUP(3.14159, 3)          3.142

  return [
    'group' => 'Math',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $number = padFormulaArgNumber ( $args [0], $env );
      $digits = padFormulaArgInt ( $args [1], $env );
      return padFormulaFirstError ( $number, $digits ) ?? padFormulaRoundTo ( $number, $digits, 'up' );
    }
  ];

?>
