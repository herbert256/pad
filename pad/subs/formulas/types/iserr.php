<?php

  // iserr - ISERR(value) is TRUE when the value is an error other than #N/A - #DIV/0!, #VALUE!,
  // #REF! ... - and FALSE for #N/A and for a value that is no error.
  //
  //   =ISERR(1/0)                   TRUE
  //   =ISERR(#N/A)                  FALSE
  //   =ISERR(B2)                    FALSE

  return [
    'group' => 'Information',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $value = padFormulaScalar ( $args [0], $env );
      return padFormulaIsError ( $value ) and $value ['error'] != '#N/A';
    }
  ];

?>
