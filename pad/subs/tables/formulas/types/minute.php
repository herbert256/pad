<?php

  // minute - MINUTE(time) is the minute of a time, 0 to 59: of the fraction of a day a serial holds
  // past its whole days, or of a text that reads as a time or a date and a time. The time is
  // rounded to the second first, as it shows. A serial below 0 or past 9999-12-31 is #NUM!, a
  // text that is no time #VALUE!.
  //
  //   =MINUTE("12:45:00 PM")        45
  //   =MINUTE(0.78125)              45
  //   =MINUTE("13:45"-"1:45")       0

  return [
    'group' => 'Date and time',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $serial = padFormulaDateArg ( $args [0], $env );
      return padFormulaIsError ( $serial ) ? $serial : (float) intdiv ( (int) round ( $serial * 86400 ) % 3600, 60 );
    }
  ];

?>
