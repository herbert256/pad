<?php

  // fv - FV(rate, nper, pmt, [pv], [type]) is what a loan or a saving is worth after nper periods
  // of a payment pmt at a rate, starting from pv (0); type 1 pays at the start of each period.
  // Money paid in is negative, what comes back positive.
  //
  //   =ROUND(FV(6%/12, 10, -200, -500, 1), 2)   2581.4
  //   =ROUND(FV(5%/12, 120, -100), 2)           15528.23

  return [
    'group' => 'Finance',
    'args'  => [ 3, 5 ],
    'call'  => function ( $args, $env ) {
      $terms = padFormulaFinance ( $args, [ 0.0, 0.0, 0.0, 0.0, 0.0 ], $env );
      if ( padFormulaIsError ( $terms ) )
        return $terms;
      [ $rate, $nper, $pmt, $pv, $type ] = $terms;
      return -padFormulaAnnuity ( $rate, $nper, $pmt, $pv, 0.0, $type != 0 ? 1 : 0 );
    }
  ];

?>
