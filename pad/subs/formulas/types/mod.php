<?php

  // mod - MOD(number, divisor) is the remainder of a division, with the sign of the divisor:
  // number - divisor * INT(number / divisor). A divisor of 0 is #DIV/0!.
  //
  //   =MOD(10, 3)                   1
  //   =MOD(-3, 2)                   1
  //   =MOD(3, -2)                   -1
  //   =MOD(5, 0)                    #DIV/0!

  return [
    'group' => 'Math',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $number  = padFormulaArgNumber ( $args [0], $env );
      $divisor = padFormulaArgNumber ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $number, $divisor ) )
        return $error;
      if ( $divisor == 0 )
        return padFormulaError ( '#DIV/0!' );
      return padFormulaNearZero ( $number - $divisor * floor ( padFormulaRound15 ( $number / $divisor ) ), $number, $divisor );
    }
  ];

?>
