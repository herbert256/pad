<?php

  // day - DAY(date) is the day of the month of a date, 1 to 31.
  //
  //   =DAY(D3)                      29
  //   =DAY(45306)                   15

  return [
    'group' => 'Date and time',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $serial = padFormulaDateArg ( $args [0], $env );
      return padFormulaIsError ( $serial ) ? $serial : (float) padFormulaYmd ( $serial ) [2];
    }
  ];

?>
