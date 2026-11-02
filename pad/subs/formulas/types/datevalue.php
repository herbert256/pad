<?php

  // datevalue - DATEVALUE(text) is the serial of the date a text says - 2024-01-15, 1/15/2024 (the
  // month first), 15-Jan-2024, January 15, 2024 - its time of day left out, shown as a date. A text
  // that reads as no date of 1900 to 9999 - a time alone too - and a value that is no text - a
  // number, a cell holding a date - is #VALUE!; so is a date without a year, which Excel reads in
  // the year of its clock.
  //
  //   =DATEVALUE("8/22/2011")*1               40777
  //   =DATEVALUE("22-MAY-2011")               2011-05-22
  //   =DATEVALUE("2011/02/23 13:45")          2011-02-23
  //   =DATEVALUE(D2)                          #VALUE!

  return [
    'group' => 'Date and time',
    'args'  => [ 1, 1 ],
    'call'  => function ( $args, $env ) {
      $text = padFormulaScalar ( $args [0], $env );
      if ( padFormulaIsError ( $text ) )
        return $text;
      $serial = is_string ( $text ) ? padFormulaParseDate ( $text ) : NULL;
      // Below 1 the text was a time alone - no date.
      return ( $serial === NULL or $serial < 1 ) ? padFormulaError ( '#VALUE!' ) : (float) floor ( $serial );
    }
  ];

?>
