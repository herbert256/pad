<?php

  // Membership test for the not sequence, ~1, ~2, ~3, ... = -2, -3, -4, ...: a whole number
  // of -2 or less. build/check.php asks it for {keep not}, {remove not} and {flag not}.
  // Without it the check read the 10,000 terms of the PADnot table, which ends at -10001 and
  // does not climb, and so was taken as the whole sequence: -10002 and every term below it
  // were answered no.

  function pqBoolNot ( $n, $p=0 ) {

    return pqBoolWhole ( $n ) and $n <= -2;

  }

?>
