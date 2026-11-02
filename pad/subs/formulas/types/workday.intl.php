<?php

  // workday.intl - WORKDAY.INTL(start, days, [weekend], [holidays]) is the date a number of
  // workdays after a date as WORKDAY finds it, with a weekend of its own: a number, 1 (the
  // default) Saturday and Sunday, 2 Sunday and Monday ... 7 Friday and Saturday, 11 Sunday alone
  // ... 17 Saturday alone - another number is #NUM! - or a text of seven 0s and 1s from Monday,
  // 1 a day off: "0000110" is Friday and Saturday. A text of another kind, and "1111111", which
  // leaves no workday, are #VALUE!.
  //
  //   =WORKDAY.INTL(D2, 10, 11)                2024-01-26
  //   =WORKDAY.INTL(D2, 10, "0000110")         2024-01-29
  //   =WORKDAY.INTL(D2, -10, "1000001")        2024-01-02
  //   =WORKDAY.INTL(D2, 10, "1111111")         #VALUE!

  return [
    'group'  => 'Date and time',
    'args'   => [ 2, 4 ],
    'format' => 'yyyy-mm-dd',
    'call'   => function ( $args, $env ) {
      $start    = padFormulaDateArg ( $args [0], $env );
      $days     = padFormulaArgInt ( $args [1], $env );
      $weekend  = padFormulaWeekend ( $args [2] ?? NULL, $env );
      $holidays = padFormulaHolidays ( $args [3] ?? NULL );
      if ( $error = padFormulaFirstError ( $start, $days, $weekend, $holidays ) )
        return $error;
      if ( ! in_array ( FALSE, $weekend, TRUE ) )
        return padFormulaError ( '#VALUE!' );
      return padFormulaWorkday ( (int) floor ( $start ), $days, $weekend, $holidays );
    }
  ];

?>
