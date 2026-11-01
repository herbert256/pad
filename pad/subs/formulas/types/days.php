<?php

  // days - DAYS(end, start) is the number of days from start to end - dates as serials or as texts
  // that read as dates.
  //
  //   =DAYS(D6, D2)                       351
  //   =DAYS("2024-03-01", "2024-02-01")   29

  return [
    'group' => 'Date and time',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $end   = padFormulaDateArg ( $args [0], $env );
      $start = padFormulaDateArg ( $args [1], $env );
      return padFormulaFirstError ( $end, $start ) ?? floor ( $end ) - floor ( $start );
    }
  ];

?>
