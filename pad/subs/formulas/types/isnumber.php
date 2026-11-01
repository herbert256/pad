<?php

  // isnumber - ISNUMBER(value) is TRUE when the value is a number - a date and a time are numbers -
  // and FALSE for anything else: a text that reads as a number too, an empty cell, an error.
  //
  //   =ISNUMBER(B2)                 TRUE
  //   =ISNUMBER(D2)                 TRUE
  //   =ISNUMBER("19")               FALSE
  //   =ISNUMBER(A2)                 FALSE

  return [
    'group' => 'Information',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $value = padFormulaScalar ( $args [0], $env );
      return is_float ( $value ) or is_int ( $value );
    }
  ];

?>
