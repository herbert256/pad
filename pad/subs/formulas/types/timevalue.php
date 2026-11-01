<?php

  // timevalue - TIMEVALUE(text) is the time of day a text says, as the fraction of a day: 13:45,
  // 1:45 PM, 13:45:30, alone or after a date, whose date is left out - a date alone is 0. A text
  // that reads as no time, and a value that is no text - a number, a cell holding a time - is
  // #VALUE!.
  //
  //   =TIMEVALUE("2:24 AM")                   0.1
  //   =TIMEVALUE("22-Aug-2011 6:35 AM")       0.274305556
  //   =TIMEVALUE("18:45")*24                  18.75
  //   =TIMEVALUE(0.5)                         #VALUE!

  return [
    'group' => 'Date and time',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $text = padFormulaScalar ( $args [0], $env );
      if ( padFormulaIsError ( $text ) )
        return $text;
      $serial = is_string ( $text ) ? padFormulaParseDate ( $text ) : NULL;
      if ( $serial === NULL )
        return padFormulaError ( '#VALUE!' );
      // The seconds past midnight to a millionth: a date's whole days took the last digits of
      // the fraction, 6:35 AM after a date of 2011 was 0.274305555554747.
      return round ( ( $serial - floor ( $serial ) ) * 86400, 6 ) / 86400;
    }
  ];

?>
