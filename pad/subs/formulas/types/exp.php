<?php

  // exp - EXP(number) is e raised to the number.
  //
  //   =EXP(1)                       2.718281828
  //   =EXP(0)                       1

  return [
    'group' => 'Math',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $number = padFormulaArgNumber ( $args [0], $env );
      return padFormulaIsError ( $number ) ? $number : exp ( $number );
    }
  ];

?>
