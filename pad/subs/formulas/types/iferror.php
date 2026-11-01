<?php

  // iferror - IFERROR(value, value_if_error) is the value, or the other one when it is an error -
  // any of them.
  //
  //   =IFERROR(1/0, "none")         none
  //   =IFERROR(4/2, "none")         2

  return [
    'group' => 'Logic',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $value = padFormulaScalar ( $args [0], $env );
      return padFormulaIsError ( $value ) ? $args [1] : $value;
    }
  ];

?>
