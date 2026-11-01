<?php

  // power - POWER(number, power) is a number raised to a power, as ^ does: 0 to the power 0 and a
  // negative number to a fraction are #NUM!, 0 to a negative power #DIV/0!.
  //
  //   =POWER(2, 10)                 1024
  //   =POWER(4, 0.5)                2
  //   =POWER(-8, 1/3)               #NUM!

  return [
    'group' => 'Math',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $base     = padFormulaArgNumber ( $args [0], $env );
      $exponent = padFormulaArgNumber ( $args [1], $env );
      return padFormulaFirstError ( $base, $exponent ) ?? padFormulaPow ( $base, $exponent );
    }
  ];

?>
