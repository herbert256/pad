<?php

  // edate - EDATE(start, months) is the same day months later - earlier for a negative number - or
  // the last day of that month when it is shorter.
  //
  //   =EDATE("2024-01-31", 1)       2024-02-29
  //   =EDATE(D2, -2)                2023-11-15

  return [
    'group' => 'Date and time',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $serial = padFormulaDateArg ( $args [0], $env );
      $months = padFormulaArgInt ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $serial, $months ) )
        return $error;
      [ $year, $month, $day ] = padFormulaYmd ( $serial );
      $last   = padFormulaYmd ( padFormulaSerial ( $year, $month + $months + 1, 0 ) ) [2];
      $result = padFormulaSerial ( $year, $month + $months, min ( $day, $last ) );
      return ( $result < 0 or $result > padFormulaLastSerial ) ? padFormulaError ( '#NUM!' ) : (float) $result;
    }
  ];

?>
