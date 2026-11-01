<?php

  // istext - ISTEXT(value) is TRUE when the value is a text - the empty text "" too - and FALSE
  // for anything else: a number, a logical value, an empty cell, an error.
  //
  //   =ISTEXT(A2)                   TRUE
  //   =ISTEXT(B2)                   FALSE
  //   =ISTEXT(A8)                   FALSE

  return [
    'group' => 'Information',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      return is_string ( padFormulaScalar ( $args [0], $env ) );
    }
  ];

?>
