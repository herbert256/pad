<?php

  // isblank - ISBLANK(value) is TRUE when the value is a reference to an empty cell - a cell past
  // the sheet's is one too - and FALSE for anything else: a text, even the empty text "" a formula
  // answers, a 0, an error. A range of more cells stands for its cell in the formula's row or
  // column.
  //
  //   =ISBLANK(A8)                  TRUE
  //   =ISBLANK(A2)                  FALSE
  //   =ISBLANK("")                  FALSE

  return [
    'group' => 'Information',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      return padFormulaIsRange ( $args [0] ) and padFormulaScalar ( $args [0], $env ) === NULL;
    }
  ];

?>
