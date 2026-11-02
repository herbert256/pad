<?php

  // CEILING, FLOOR and MROUND (padFormulaMultiple) of a number so far below the significance
  // that the quotient is too small for a float: a positive number rounded up is the
  // significance, as CEILING(0.0001,1E10) is, a negative one rounded down by FLOOR with a
  // positive significance minus it, CEILING of two negatives minus it too; toward zero and to
  // the nearest it is 0, never -0. Before, the quotient was 0, and so was every answer.

  $cases = formulasRun ( [
    '=CEILING(1E-200,1E200)',
    '=CEILING(1E-300,1E10)',
    '=CEILING(-1E-300,-1E10)',
    '=CEILING(-1E-300,1E10)',
    '=FLOOR(-1E-300,1E10)',
    '=FLOOR(1E-300,1E10)',
    '=FLOOR(-1E-300,-1E10)',
    '=MROUND(1E-300,1E10)',
    '=MROUND(-1E-300,-1E10)',
    '=CEILING(0.0001,1E10)'
  ] );

?>
