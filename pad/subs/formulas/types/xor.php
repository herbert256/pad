<?php

  // xor - XOR(logical, ...) is TRUE when an odd number of the values are TRUE, read as AND reads
  // them.
  //
  //   =XOR(TRUE, TRUE, TRUE)        TRUE
  //   =XOR(TRUE, TRUE)              FALSE

  return [
    'group' => 'Logic',
    'args'  => [ 1, 254 ],
    'call'  => function ( $args ) {
      $values = padFormulaLogicals ( $args );
      return padFormulaIsError ( $values ) ? $values : count ( array_filter ( $values ) ) % 2 == 1;
    }
  ];

?>
