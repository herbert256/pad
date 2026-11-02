<?php

  // ipmt - IPMT(rate, per, nper, pv, [fv], [type]) is the interest part of the payment of period
  // per - from 1 to nper, else #NUM! - of a loan or a saving whose payment PMT works out: rate the
  // interest of a period, pv what it is worth now, fv at the end (0), type 1 to pay at the start
  // of each period (0, at the end), when the first period pays no interest. The interest of a
  // period is that of what is still owed at its start - what FV gives after the periods before
  // it - so it shrinks as the loan is paid off. Money paid out is negative, as in Excel.
  //
  //   =ROUND(IPMT(10%/12, 1, 36, 8000), 2)     -66.67
  //   =ROUND(IPMT(10%, 3, 3, 8000), 2)         -292.45
  //   =IPMT(10%, 1, 3, 8000, 0, 1)             0
  //   =IPMT(10%, 4, 3, 8000)                   #NUM!

  return [
    'group' => 'Finance',
    'args'  => [ 4, 6 ],
    'call'  => function ( $args, $env ) {
      $terms = padFormulaFinance ( $args, [ 0.0, 0.0, 0.0, 0.0, 0.0, 0.0 ], $env );
      if ( padFormulaIsError ( $terms ) )
        return $terms;
      [ $rate, $per, $nper, $pv, $fv, $type ] = $terms;
      $type = $type != 0 ? 1 : 0;
      if ( $per < 1 or $per > $nper )
        return padFormulaError ( '#NUM!' );
      $pmt = padFormulaType ( 'pmt' ) ['call'] ( [ $rate, $nper, $pv, $fv, (float) $type ], $env );
      if ( padFormulaIsError ( $pmt ) )
        return $pmt;
      if ( $per == 1 )
        return $type ? 0.0 : -$pv * $rate;
      // What FV says is owed after the periods before this one - paid at the start, the payment
      // of this period is made before its interest is counted.
      $owed = $type ? -padFormulaAnnuity ( $rate, $per - 2, $pmt, $pv, 0.0, 1 ) - $pmt
                    : -padFormulaAnnuity ( $rate, $per - 1, $pmt, $pv, 0.0, 0 );
      return $owed * $rate;
    }
  ];

?>
