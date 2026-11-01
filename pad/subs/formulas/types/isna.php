<?php

  // isna - ISNA(value) is TRUE when the value is the error #N/A - a lookup that found nothing
  // answers it - and FALSE for another error and for a value that is no error.
  //
  //   =ISNA(VLOOKUP("Kiwis", A2:C7, 2, FALSE))   TRUE
  //   =ISNA(#N/A)                   TRUE
  //   =ISNA(1/0)                    FALSE

  return [
    'group' => 'Information',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $value = padFormulaScalar ( $args [0], $env );
      return padFormulaIsError ( $value ) and $value ['error'] == '#N/A';
    }
  ];

?>
