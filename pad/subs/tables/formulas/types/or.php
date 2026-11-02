<?php

  // or - OR(logical, ...) is TRUE when one of the values is, read as AND reads them.
  //
  //   =OR(FALSE, 0, "TRUE")         TRUE
  //   =OR(FALSE, 0)                 FALSE

  return [
    'group' => 'Logic',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $values = padFormulaLogicals ( $args );
      return padFormulaIsError ( $values ) ? $values : in_array ( TRUE, $values, TRUE );
    }
  ];

?>
