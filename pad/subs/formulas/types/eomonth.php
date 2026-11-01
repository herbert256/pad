<?php

  // eomonth - EOMONTH(start, months) is the last day of the month months after the month of start -
  // before it for a negative number.
  //
  //   =EOMONTH(D2, 1)               2024-02-29
  //   =EOMONTH("2024-03-15", -1)    2024-02-29

  return [
    'group' => 'Date and time',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $serial = padFormulaDateArg ( $args [0], $env );
      $months = padFormulaArgInt ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $serial, $months ) )
        return $error;
      [ $year, $month ] = padFormulaYmd ( $serial );
      $end = padFormulaSerial ( $year, $month + $months + 1, 0 );
      return ( $end < 0 or $end > padFormulaLastSerial ) ? padFormulaError ( '#NUM!' ) : (float) $end;
    }
  ];

?>
