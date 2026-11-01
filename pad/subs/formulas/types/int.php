<?php

  // int - INT(number) rounds down to the whole number below - toward minus infinity, so -1.5 is
  // -2.
  //
  //   =INT(8.9)                     8
  //   =INT(-1.5)                    -2

  return [
    'group' => 'Math',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $number = padFormulaArgNumber ( $args [0], $env );
      return padFormulaIsError ( $number ) ? $number : floor ( padFormulaRound15 ( $number ) );
    }
  ];

?>
