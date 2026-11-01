<?php

  // iserror - ISERROR(value) is TRUE when the value is an error - any of them, #N/A too - and FALSE
  // when it is not: it takes the error it is given, which never passes on - ISERROR(1/0) is TRUE,
  // not #DIV/0!.
  //
  //   =ISERROR(1/0)                 TRUE
  //   =ISERROR(#N/A)                TRUE
  //   =ISERROR(B2)                  FALSE

  return [
    'group' => 'Information',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      return padFormulaIsError ( padFormulaScalar ( $args [0], $env ) );
    }
  ];

?>
