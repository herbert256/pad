<?php

  // networkdays.intl - NETWORKDAYS.INTL(start, end, [weekend], [holidays]) counts the workdays
  // from one date to another as NETWORKDAYS does, with a weekend of its own: a number, 1 (the
  // default) Saturday and Sunday, 2 Sunday and Monday ... 7 Friday and Saturday, 11 Sunday alone
  // ... 17 Saturday alone - another number is #NUM! - or a text of seven 0s and 1s from Monday,
  // 1 a day off: "0000110" is Friday and Saturday, "1111111" leaves no workday and counts 0;
  // another text is #VALUE!.
  //
  //   =NETWORKDAYS.INTL(D2, D3)                34
  //   =NETWORKDAYS.INTL(D2, D3, 11)            40
  //   =NETWORKDAYS.INTL(D2, D3, 11, D3)        39
  //   =NETWORKDAYS.INTL(D2, D3, "0010001")     33
  //   =NETWORKDAYS.INTL(D2, D3, 0)             #NUM!

  return [
    'group' => 'Date and time',
    'args'  => [ 2, 4 ],
    'call'  => function ( $args, $env ) {
      $start    = padFormulaDateArg ( $args [0], $env );
      $end      = padFormulaDateArg ( $args [1], $env );
      $weekend  = padFormulaWeekend ( $args [2] ?? NULL, $env );
      $holidays = padFormulaHolidays ( $args [3] ?? NULL );
      if ( $error = padFormulaFirstError ( $start, $end, $weekend, $holidays ) )
        return $error;
      return padFormulaWorkdays ( (int) floor ( $start ), (int) floor ( $end ), $weekend, $holidays );
    }
  ];

?>
