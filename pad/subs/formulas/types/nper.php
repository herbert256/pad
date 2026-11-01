<?php

  // nper - NPER(rate, pmt, pv, [fv], [type]) is the number of periods a payment pmt takes to bring
  // pv to fv (0) at a rate; type 1 pays at the start of each period. Terms that never get there
  // are #NUM!.
  //
  //   =ROUND(NPER(1%, -100, -1000, 10000, 1), 4)   59.6739
  //   =ROUND(NPER(1%, -100, -1000), 4)             -9.5786

  return [
    'group' => 'Finance',
    'args'  => [ 3, 5 ],
    'call'  => function ( $args, $env ) {
      $terms = padFormulaFinance ( $args, [ 0.0, 0.0, 0.0, 0.0, 0.0 ], $env );
      if ( padFormulaIsError ( $terms ) )
        return $terms;
      [ $rate, $pmt, $pv, $fv, $type ] = $terms;
      $type = $type != 0 ? 1 : 0;
      if ( $rate == 0 )
        return $pmt == 0 ? padFormulaError ( '#NUM!' ) : -( $pv + $fv ) / $pmt;
      $top    = $pmt * ( 1 + $rate * $type ) - $fv * $rate;
      $bottom = $pmt * ( 1 + $rate * $type ) + $pv * $rate;
      if ( $bottom == 0 or $top / $bottom <= 0 or $rate <= -1 )
        return padFormulaError ( '#NUM!' );
      return log ( $top / $bottom ) / log ( 1 + $rate );
    }
  ];

?>
