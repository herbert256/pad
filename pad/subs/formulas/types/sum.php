<?php

  // sum - SUM(number, ...) adds up numbers. A number typed into the call counts, and so do TRUE
  // (as 1) and a text that reads as a number; of a range only the numbers count - its text,
  // logical values and empty cells are left out. An error among them is the answer.
  //
  //   =SUM(1, 2, 3)                 6
  //   =SUM(B2:B7)                   58
  //   =SUM("4", TRUE, B2)           15

  return [
    'group' => 'Math',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $numbers = padFormulaNumbers ( $args );
      return padFormulaIsError ( $numbers ) ? $numbers : (float) array_sum ( $numbers );
    }
  ];

?>
