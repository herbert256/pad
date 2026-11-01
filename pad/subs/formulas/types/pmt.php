<?php

  // pmt - PMT(rate, nper, pv, [fv], [type]) is the payment of each period of a loan or a saving -
  // rate the interest of a period, nper the number of periods, pv what it is worth now, fv what
  // it is worth at the end (0), type 1 to pay at the start of each period (0, at the end). Money
  // paid out is negative, as in Excel: a loan of 10000 is paid off with a payment below 0.
  //
  //   =ROUND(PMT(8%/12, 10, 10000), 2)         -1037.03
  //   =ROUND(PMT(8%/12, 10, 10000, 0, 1), 2)   -1030.16

  return [
    'group' => 'Finance',
    'args'  => [ 3, 5 ],
    'call'  => function ( $args, $env ) {
      $terms = padFormulaFinance ( $args, [ 0.0, 0.0, 0.0, 0.0, 0.0 ], $env );
      if ( padFormulaIsError ( $terms ) )
        return $terms;
      [ $rate, $nper, $pv, $fv, $type ] = $terms;
      $type = $type != 0 ? 1 : 0;
      if ( $nper == 0 )
        return padFormulaError ( '#NUM!' );
      if ( $rate == 0 )
        return -( $pv + $fv ) / $nper;
      $grow = ( 1 + $rate ) ** $nper;
      return -( $rate * ( $fv + $pv * $grow ) ) / ( ( 1 + $rate * $type ) * ( $grow - 1 ) );
    }
  ];

?>
