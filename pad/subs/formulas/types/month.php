<?php

  // month - MONTH(date) is the month of a date, 1 to 12.
  //
  //   =MONTH(D3)                    2
  //   =MONTH("15 March 2024")       3

  return [
    'group' => 'Date and time',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $serial = padFormulaDateArg ( $args [0], $env );
      return padFormulaIsError ( $serial ) ? $serial : (float) padFormulaYmd ( $serial ) [1];
    }
  ];

?>
