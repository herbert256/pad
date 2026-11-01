<?php

  // ifna - IFNA(value, value_if_na) is the value, or the other one when it is #N/A; another error
  // stays.
  //
  //   =IFNA(#N/A, 0)                0
  //   =IFNA(1/0, 0)                 #DIV/0!

  return [
    'group' => 'Logic',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $value = padFormulaScalar ( $args [0], $env );
      return ( padFormulaIsError ( $value ) and $value ['error'] == '#N/A' ) ? $args [1] : $value;
    }
  ];

?>
