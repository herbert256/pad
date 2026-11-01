<?php

  // weekday - WEEKDAY(date, [type]) is the day of the week of a date as a number: type 1 (the
  // default) Sunday 1 to Saturday 7, 2 Monday 1 to Sunday 7, 3 Monday 0 to Sunday 6, 11 to 17 the
  // week starting on Monday to Sunday at 1. Another type is #NUM!.
  //
  //   =WEEKDAY(D2)                  2
  //   =WEEKDAY(D2, 2)               1
  //   =WEEKDAY(D2, 3)               0

  return [
    'group' => 'Date and time',
    'args'  => [ 1, 2 ],
    'call'  => function ( $args, $env ) {
      $serial = padFormulaDateArg ( $args [0], $env );
      $type   = isset ( $args [1] ) ? padFormulaArgInt ( $args [1], $env ) : 1;
      if ( $error = padFormulaFirstError ( $serial, $type ) )
        return $error;
      $day = padFormulaWeekday ( $serial );
      return match ( $type ) {
        1, 17   => (float) ( $day + 1 ),
        2, 11   => (float) ( ( $day + 6 ) % 7 + 1 ),
        3       => (float) ( ( $day + 6 ) % 7 ),
        12      => (float) ( ( $day + 5 ) % 7 + 1 ),
        13      => (float) ( ( $day + 4 ) % 7 + 1 ),
        14      => (float) ( ( $day + 3 ) % 7 + 1 ),
        15      => (float) ( ( $day + 2 ) % 7 + 1 ),
        16      => (float) ( ( $day + 1 ) % 7 + 1 ),
        default => padFormulaError ( '#NUM!' )
      };
    }
  ];

?>
