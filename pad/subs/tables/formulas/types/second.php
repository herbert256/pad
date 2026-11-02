<?php

  // second - SECOND(time) is the second of a time, 0 to 59: of the fraction of a day a serial holds
  // past its whole days, or of a text that reads as a time or a date and a time - rounded to the
  // nearest second, as it shows. A serial below 0 or past 9999-12-31 is #NUM!, a text that is no
  // time #VALUE!.
  //
  //   =SECOND("4:48:18 PM")         18
  //   =SECOND("4:48 PM")            0
  //   =SECOND(TIME(1, 2, 3))        3

  return [
    'group' => 'Date and time',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $serial = padFormulaDateArg ( $args [0], $env );
      return padFormulaIsError ( $serial ) ? $serial : (float) ( (int) round ( $serial * 86400 ) % 60 );
    }
  ];

?>
