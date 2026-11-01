<?php

  // isnontext - ISNONTEXT(value) is TRUE when the value is no text: a number, a logical value, an
  // empty cell, an error too; FALSE for a text.
  //
  //   =ISNONTEXT(B2)                TRUE
  //   =ISNONTEXT(A8)                TRUE
  //   =ISNONTEXT(#N/A)              TRUE
  //   =ISNONTEXT(A2)                FALSE

  return [
    'group' => 'Information',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      return ! is_string ( padFormulaScalar ( $args [0], $env ) );
    }
  ];

?>
