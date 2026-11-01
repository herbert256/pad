<?php

  // rate - RATE(nper, pmt, pv, [fv], [type], [guess]) is the interest rate of a period that makes a
  // payment pmt over nper periods bring pv to fv (0) - found by Newton's method from guess (10%),
  // as Excel finds it: when 20 steps do not settle it to within 0.0000001, #NUM!.
  //
  //   =ROUND(RATE(48, -200, 8000)*12, 4)       0.0924
  //   =ROUND(RATE(10, -1000, 7000), 4)         0.0707

  return [
    'group' => 'Finance',
    'args'  => [ 3, 6 ],
    'call'  => function ( $args, $env ) {
      $terms = padFormulaFinance ( $args, [ 0.0, 0.0, 0.0, 0.0, 0.0, 0.1 ], $env );
      if ( padFormulaIsError ( $terms ) )
        return $terms;
      [ $nper, $pmt, $pv, $fv, $type, $guess ] = $terms;
      $type = $type != 0 ? 1 : 0;
      if ( $nper <= 0 )
        return padFormulaError ( '#NUM!' );
      return padFormulaNewton ( fn ( $rate ) => padFormulaAnnuity ( $rate, $nper, $pmt, $pv, $fv, $type ), $guess );
    }
  ];

?>
