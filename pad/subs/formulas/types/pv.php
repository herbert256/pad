<?php

  // pv - PV(rate, nper, pmt, [fv], [type]) is what a series of payments is worth now - the loan a
  // payment pays off, the sum a pension costs - at a rate over nper periods, with fv left at the
  // end (0); type 1 pays at the start of each period.
  //
  //   =ROUND(PV(8%/12, 12*20, 500, , 0), 2)     -59777.15
  //   =ROUND(PV(5%/12, 120, -100), 2)           9428.14

  return [
    'group' => 'Finance',
    'args'  => [ 3, 5 ],
    'call'  => function ( $args, $env ) {
      $terms = padFormulaFinance ( $args, [ 0.0, 0.0, 0.0, 0.0, 0.0 ], $env );
      if ( padFormulaIsError ( $terms ) )
        return $terms;
      [ $rate, $nper, $pmt, $fv, $type ] = $terms;
      $type = $type != 0 ? 1 : 0;
      if ( $rate == 0 )
        return -( $fv + $pmt * $nper );
      $grow = ( 1 + $rate ) ** $nper;
      return -( $fv + $pmt * ( 1 + $rate * $type ) * ( $grow - 1 ) / $rate ) / $grow;
    }
  ];

?>
