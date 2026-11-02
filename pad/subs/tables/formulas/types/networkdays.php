<?php

  // networkdays - NETWORKDAYS(start, end, [holidays]) counts the workdays from one date to another,
  // both counted - Monday to Friday, less the holidays: a range of dates, its empty cells left out,
  // or one date. A holiday in the weekend or listed twice is left out once; a holiday that is
  // text that reads as no date, or a logical value, is #VALUE!. A start after the end counts
  // backward, below 0. The times of day are cut off.
  //
  //   =NETWORKDAYS(D2, D3)                     34
  //   =NETWORKDAYS(D3, D2)                     -34
  //   =NETWORKDAYS(D2, D7, D3:D6)              378
  //   =NETWORKDAYS("2024-12-23", "2024-12-27", "2024-12-25")   4

  return [
    'group' => 'Date and time',
    'args'  => [ 2, 3 ],
    'call'  => function ( $args, $env ) {
      $start    = padFormulaDateArg ( $args [0], $env );
      $end      = padFormulaDateArg ( $args [1], $env );
      $holidays = padFormulaHolidays ( $args [2] ?? NULL );
      if ( $error = padFormulaFirstError ( $start, $end, $holidays ) )
        return $error;
      return padFormulaWorkdays ( (int) floor ( $start ), (int) floor ( $end ), padFormulaWeekend ( NULL, $env ), $holidays );
    }
  ];

?>
