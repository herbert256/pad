<?php

  // and - AND(logical, ...) is TRUE when every value is: TRUE, a number that is not 0, the text
  // TRUE; of a range its texts and empty cells are left out. Without any logical value #VALUE!.
  //
  //   =AND(TRUE, 1, "TRUE")         TRUE
  //   =AND(TRUE, 0)                 FALSE

  return [
    'group' => 'Logic',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $values = padFormulaLogicals ( $args );
      return padFormulaIsError ( $values ) ? $values : ! in_array ( FALSE, $values, TRUE );
    }
  ];

?>
