<?php

  // The finance functions on the sample sheet (_lib/formulas.php), the examples of Excel's
  // own documentation: money paid out is negative, type 1 pays at the start of a period, a
  // rate of 0 is the plain sum; RATE and IRR are found by Newton's method from 10% (or the
  // guess given) and are #NUM! when 150 steps do not settle them - or there is nothing to
  // find. Rounded to 8 and 10 decimals, where the last of 15 digits depends on the order of
  // the arithmetic.

  $cases = formulasRun ( [
    '=ROUND(PMT(8%/12,10,10000),8)',
    '=ROUND(PMT(8%/12,10,10000,0,1),8)',
    '=PMT(0,12,1200)',
    '=PMT(5%,0,100)',
    '=ROUND(FV(6%/12,10,-200,-500,1),8)',
    '=FV(0,12,-100)',
    '=ROUND(PV(8%/12,12*20,500,,0),8)',
    '=PV(0,12,-100)',
    '=ROUND(NPER(1%,-100,-1000,10000,1),8)',
    '=ROUND(NPER(1%,-100,-1000),8)',
    '=NPER(0,-100,1000)',
    '=NPER(10%,-50,1000)',
    '=ROUND(RATE(48,-200,8000),10)',
    '=ROUND(RATE(10,-1000,7000),10)',
    '=RATE(10,1000,7000)',
    '=ROUND(NPV(10%,-10000,3000,4200,6800),8)',
    '=ROUND(NPV(8%,E3:E7)+E2,8)',
    '=NPV(-1,1,2)',
    '=ROUND(IRR(E2:E7),10)',
    '=ROUND(IRR(E2:E6),10)',
    '=ROUND(IRR(E2:E4,-10%),10)',
    '=IRR(B2:B3)'
  ] );

?>
