<?php

  // The finance functions on a rate that leaves them nothing to divide by (_lib/formulas.php):
  // -200% over 2 periods grows to 1 again, -100% paid at the start of a period leaves no
  // period to pay in, (1-90%)^400 is less than a float holds, a rate of -99.9999% discounts
  // 60 periods past one, -100% over periods before now is 0 to a negative power - each is
  // #NUM!, a number that cannot be computed, where PHP's division by 0, or its deprecation of
  // that power, ended the request: a visitor typing -2400% as the interest of the sheets loan
  // was answered 500. A rate too small for 1+rate to differ from 1 counts as the rate it
  // is - PMT, PV, FV and NPER of 1E-17 are those of a rate of 0 to 15 digits - where
  // (1+rate)^nper - 1 was 0. IRR from a guess of -100% is #NUM!.

  $cases = formulasRun ( [
    '=PMT(-2,2,100)',
    '=PMT(-1,12,100,0,1)',
    '=PMT(-1,-12,100)',
    '=ROUND(PMT(1E-17,12,1000),8)',
    '=PV(-1,10,100)',
    '=PV(-0.9,400,1)',
    '=ROUND(PV(1E-17,12,-100),8)',
    '=ROUND(FV(1E-17,12,-100),8)',
    '=ROUND(NPER(1E-20,-100,1000),8)',
    '=NPV(-0.999999,' . implode ( ',', array_fill ( 0, 60, 1 ) ) . ')',
    '=IRR(E2:E4,-1)',
    '=ROUND(PMT(8%/12,10,10000),8)'
  ] );

?>
