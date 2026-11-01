<?php

  // abs - ABS(number) is a number without its sign.
  //
  //   =ABS(-4.5)                    4.5
  //   =ABS(3)                       3

  return [
    'group' => 'Math',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $number = padFormulaArgNumber ( $args [0], $env );
      return padFormulaIsError ( $number ) ? $number : abs ( $number );
    }
  ];

?>
