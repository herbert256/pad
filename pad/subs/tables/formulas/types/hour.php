<?php

  // hour - HOUR(time) is the hour of a time, 0 to 23: of the fraction of a day a serial holds past
  // its whole days, or of a text that reads as a time or a date and a time. The time is rounded to
  // the second first, as it shows - 13:45 less 1:45 is 12. A date alone is midnight, 0; a serial
  // below 0 or past 9999-12-31 is #NUM!, a text that is no time #VALUE!.
  //
  //   =HOUR(0.75)                   18
  //   =HOUR("2011-07-18 7:45")      7
  //   =HOUR("6:45 PM")              18
  //   =HOUR(D2)                     0

  return [
    'group' => 'Date and time',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $serial = padFormulaDateArg ( $args [0], $env );
      return padFormulaIsError ( $serial ) ? $serial : (float) intdiv ( (int) round ( $serial * 86400 ) % 86400, 3600 );
    }
  ];

?>
