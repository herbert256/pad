<?php

  // CEILING, FLOOR, TRUNC and MROUND (types/ceiling.php, floor.php, trunc.php, mround.php) on
  // the sample sheet (_lib/formulas.php): first the examples of Microsoft's pages with their
  // answers, then the edges - Excel's signs (a positive number with a negative significance
  // #NUM!, a negative one rounded away from or toward zero by the sign of the significance), a
  // significance of 0 (CEILING 0, FLOOR #DIV/0! but for 0), MROUND's signs, its 0 and its
  // logical values, and binary fractions rounded as they show, to 15 digits: CEILING(2.5,0.1)
  // is 2.5, not 2.6, FLOOR(0.3,0.1) 0.3, not 0.2. A 0 that a negative number rounds to is 0.

  $cases = formulasRun ( [
    '=CEILING(2.5,1)',
    '=CEILING(-2.5,-2)',
    '=CEILING(-2.5,2)',
    '=CEILING(1.5,0.1)',
    '=CEILING(0.234,0.01)',
    '=FLOOR(3.7,2)',
    '=FLOOR(-2.5,-2)',
    '=FLOOR(2.5,-2)',
    '=FLOOR(1.58,0.1)',
    '=FLOOR(0.234,0.01)',
    '=TRUNC(8.9)',
    '=TRUNC(-8.9)',
    '=TRUNC(0.45)',
    '=MROUND(10,3)',
    '=MROUND(-10,-3)',
    '=MROUND(1.3,0.2)',
    '=MROUND(5,-2)',
    '=CEILING(2.5,0.1)',
    '=CEILING(4.42,0.05)',
    '=CEILING(-4.5,-1)',
    '=CEILING(-0.25,1)',
    '=CEILING(7,0)',
    '=CEILING(-7,0)',
    '=CEILING(0,-1)',
    '=CEILING(C2,0.25)',
    '=CEILING(A9,1)',
    '=CEILING(TRUE,1)',
    '=CEILING("x",1)',
    '=CEILING(1/0,1)',
    '=CEILING(2,#N/A)',
    '=FLOOR(0.3,0.1)',
    '=FLOOR(4.45,0.05)',
    '=FLOOR(-2.5,2)',
    '=FLOOR(-4.5,-1)',
    '=FLOOR(-0.5,-1)',
    '=FLOOR(5,0)',
    '=FLOOR(-5,0)',
    '=FLOOR(0,0)',
    '=FLOOR(0,-2)',
    '=FLOOR(D2,7)',
    '=TRUNC(1234.567,-2)',
    '=TRUNC(-1234.567,-2)',
    '=TRUNC(PI(),4)',
    '=TRUNC(1.005,2)',
    '=TRUNC(0.1+0.2,1)',
    '=TRUNC(-0.5)',
    '=TRUNC(5,)',
    '=TRUNC(2.71828,1.9)',
    '=TRUNC("x")',
    '=MROUND(-10,3)',
    '=MROUND(10,-3)',
    '=MROUND(2.5,1)',
    '=MROUND(-2.5,-1)',
    '=MROUND(5,0)',
    '=MROUND(0,-3)',
    '=MROUND(C4,0.2)',
    '=MROUND("6",4)',
    '=MROUND(TRUE,1)',
    '=MROUND(1,FALSE)',
    '=MROUND(1/0,TRUE)',
    '=MROUND("x",1)'
  ] );

?>
