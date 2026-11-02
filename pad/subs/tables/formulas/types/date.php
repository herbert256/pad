<?php

  // date - DATE(year, month, day) is the serial of a date - the days since 1899-12-30 in Excel's
  // 1900 system, with its 29 February 1900. A month past 12 or a day past the month's end rolls
  // over, a 0 or less rolls back; a year below 1900 is counted from 1900 as Excel counts it, one
  // below 0 or past 9999 is #NUM!.
  //
  //   =DATE(2024, 2, 30)            2024-03-01
  //   =DATE(2024, 13, 1)            2025-01-01
  //   =DATE(2024, 1, 15)*1          45306

  return [
    'group' => 'Date and time',
    'args'  => [ 3, 3 ],
    'call'  => function ( $args, $env ) {
      $year  = padFormulaArgInt ( $args [0], $env );
      $month = padFormulaArgInt ( $args [1], $env );
      $day   = padFormulaArgInt ( $args [2], $env );
      if ( $error = padFormulaFirstError ( $year, $month, $day ) )
        return $error;
      if ( $year < 0 or $year > 9999 )
        return padFormulaError ( '#NUM!' );
      if ( $year < 1900 )
        $year += 1900;
      $serial = padFormulaSerial ( $year, $month, $day );
      return ( $serial < 0 or $serial > padFormulaLastSerial ) ? padFormulaError ( '#NUM!' ) : (float) $serial;
    }
  ];

?>
