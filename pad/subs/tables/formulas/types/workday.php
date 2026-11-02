<?php

  // workday - WORKDAY(start, days, [holidays]) is the date a number of workdays after a date -
  // Monday to Friday, passing over the holidays: a range of dates, its empty cells left out, or
  // one date. Days below 0 go back, 0 is the start itself, a weekend day too; days are cut to a
  // whole number toward 0, and a date before 1900 or past 9999-12-31 is #NUM!. A holiday that is
  // text that reads as no date, or a logical value, is #VALUE!.
  //
  //   =WORKDAY(D2, 10)                         2024-01-29
  //   =WORKDAY(D3, -1)                         2024-02-28
  //   =WORKDAY(D4, 0)                          2024-03-31
  //   =WORKDAY("2024-12-24", 1, "2024-12-25")  2024-12-26

  return [
    'group'  => 'Date and time',
    'args'   => [ 2, 3 ],
    'format' => 'yyyy-mm-dd',
    'call'   => function ( $args, $env ) {
      $start    = padFormulaDateArg ( $args [0], $env );
      $days     = padFormulaArgInt ( $args [1], $env );
      $holidays = padFormulaHolidays ( $args [2] ?? NULL );
      if ( $error = padFormulaFirstError ( $start, $days, $holidays ) )
        return $error;
      return padFormulaWorkday ( (int) floor ( $start ), $days, padFormulaWeekend ( NULL, $env ), $holidays );
    }
  ];

?>
