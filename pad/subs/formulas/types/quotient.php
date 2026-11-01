<?php

  // quotient - QUOTIENT(numerator, denominator) is the whole part of a division, cut toward 0 -
  // the remainder left out, as MOD gives it for numbers of one sign. The division is taken on its
  // 15 digits, so 0.3/0.1 is 3. A denominator of 0 is #DIV/0!.
  //
  //   =QUOTIENT(5, 2)               2
  //   =QUOTIENT(4.5, 3.1)           1
  //   =QUOTIENT(-10, 3)             -3
  //   =QUOTIENT(B4, B3)             4
  //   =QUOTIENT(1, 0)               #DIV/0!

  return [
    'group' => 'Math',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $numerator   = padFormulaArgNumber ( $args [0], $env );
      $denominator = padFormulaArgNumber ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $numerator, $denominator ) )
        return $error;
      if ( $denominator == 0 )
        return padFormulaError ( '#DIV/0!' );
      $whole = padFormulaRound15 ( $numerator / $denominator );
      $whole = $whole < 0 ? ceil ( $whole ) : floor ( $whole );
      return $whole == 0 ? 0.0 : $whole;
    }
  ];

?>
