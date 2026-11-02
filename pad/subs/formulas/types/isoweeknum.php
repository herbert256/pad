<?php

  // isoweeknum - ISOWEEKNUM(date) returns the ISO week, with Monday as its first day
  // and the first Thursday identifying week one. It uses the sheet's serial calendar.
  //
  //   =ISOWEEKNUM(DATE(2021, 1, 1))  53
  //   =ISOWEEKNUM(DATE(2021, 1, 4))  1

  return [ 'group' => 'Date and time', 'args' => [ 1, 1 ], 'call' => function ( $args, $env ) {
    $serial = padFormulaDateArg ( $args [0], $env );
    if ( padFormulaIsError ( $serial ) ) return $serial;
    // Match the engine's 1900 serial calendar, including its fictitious leap day.
    $day = (int) floor ( $serial );
    $monday = ( padFormulaWeekday ( $day ) + 6 ) % 7;
    $thursday = $day + 3 - $monday;
    $year = padFormulaYmd ( $thursday ) [0];
    $jan4 = padFormulaSerial ( $year, 1, 4 );
    $first = $jan4 - ( padFormulaWeekday ( $jan4 ) + 6 ) % 7;
    return (float) ( 1 + intdiv ( $day - $first, 7 ) );
  } ];
?>
