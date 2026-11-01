<?php

  // year - YEAR(date) is the year of a date - a serial, or a text that reads as a date.
  //
  //   =YEAR(D3)                     2024
  //   =YEAR("2025-06-30")           2025

  return [
    'group' => 'Date and time',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $serial = padFormulaDateArg ( $args [0], $env );
      return padFormulaIsError ( $serial ) ? $serial : (float) padFormulaYmd ( $serial ) [0];
    }
  ];

?>
