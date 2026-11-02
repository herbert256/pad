<?php

  // RATE and IRR find the rate of a mortgage paid monthly over 15 to 40 years from no guess
  // (_lib/formulas.php), as spreadsheets do: Newton's method from 10% on the annuity equation
  // moves about 1/nper of the way each step - 22 steps for 180 months, 38 for 360, 49 for 480
  // - and it was cut at 20, so each answered #NUM!, HELPERS.md's own PMT example among them.
  // IRR follows the same equation - the cash flows' value at the end - where its first step
  // from 10% on their value now landed below -100%. Rounded to 10 decimals: 5%/12 is
  // 0.0041666667.

  $results = [];

  foreach ( [ 120, 180, 240, 360, 480 ] as $months )
    $results [] = "RATE over $months months: " . padFormula ( "=ROUND(RATE($months,PMT(5%/12,$months,200000),200000),10)" );

  $results [] = 'RATE of 360 payments of 1073.64 on 200000, a year: ' . padFormula ( '=ROUND(RATE(360,-1073.64,200000)*12,6)' );

  $flows = [ [ -200000 ] ];

  for ( $month = 1; $month <= 360; $month++ )
    $flows [] = [ 1073.64 ];

  $results [] = 'IRR of -200000 and 360 payments of 1073.64, a year: ' . padFormula ( '=ROUND(IRR(A1:A361)*12,6)', $flows );

  $results = implode ( ' | ', $results );

?>
