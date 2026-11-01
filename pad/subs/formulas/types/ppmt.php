<?php

  // ppmt - PPMT(rate, per, nper, pv, [fv], [type]) is the principal part of the payment of period
  // per - from 1 to nper, else #NUM! - of a loan or a saving: the payment PMT works out less the
  // interest IPMT gives for that period, so IPMT and PPMT of a period add up to PMT, and the
  // principal grows as the interest shrinks. The arguments are those of IPMT; money paid out is
  // negative, as in Excel.
  //
  //   =ROUND(PPMT(10%/12, 1, 24, 2000), 2)     -75.62
  //   =ROUND(PPMT(8%, 10, 10, 200000), 2)      -27598.05
  //   =ROUND(PPMT(0, 2, 4, 1000), 2)           -250

  return [
    'group' => 'Finance',
    'args'  => [ 4, 6 ],
    'call'  => function ( $args, $env ) {
      $terms = padFormulaFinance ( $args, [ 0.0, 0.0, 0.0, 0.0, 0.0, 0.0 ], $env );
      if ( padFormulaIsError ( $terms ) )
        return $terms;
      [ $rate, $per, $nper, $pv, $fv, $type ] = $terms;
      $interest = padFormulaType ( 'ipmt' ) ['call'] ( $terms, $env );
      if ( padFormulaIsError ( $interest ) )
        return $interest;
      return padFormulaType ( 'pmt' ) ['call'] ( [ $rate, $nper, $pv, $fv, $type ], $env ) - $interest;
    }
  ];

?>
