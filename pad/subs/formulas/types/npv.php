<?php

  // npv - NPV(rate, value, ...) is what a series of cash flows at the end of each period is worth
  // now at a rate - the first value one period from now; values taken as SUM takes them.
  //
  //   =ROUND(NPV(10%, -10000, 3000, 4200, 6800), 2)   1188.44
  //   =ROUND(NPV(8%, E3:E7)+E2, 2)                    1390.96

  return [
    'group' => 'Finance',
    'args'  => [ 2, 255 ],
    'call'  => function ( $args, $env ) {
      $rate    = padFormulaArgNumber ( $args [0], $env );
      $numbers = padFormulaNumbers ( array_slice ( $args, 1 ) );
      if ( $error = padFormulaFirstError ( $rate, $numbers ) )
        return $error;
      if ( $rate == -1 )
        return padFormulaError ( '#DIV/0!' );
      // A rate that discounts a period past what a float holds - -99.9999% over 60 - is #NUM!.
      $total = 0.0;
      foreach ( $numbers as $period => $value ) {
        $grow = ( 1 + $rate ) ** ( $period + 1 );
        if ( $grow == 0 or ! is_finite ( $grow ) )
          return padFormulaError ( '#NUM!' );
        $total += $value / $grow;
      }
      return $total;
    }
  ];

?>
