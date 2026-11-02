<?php

  // Membership test for digitSum: every whole number from 0 up is the digit sum of some n - k
  // ones in a row add up to k - so that is the whole test. build/check.php asks it for {keep:auto
  // digitSum}, {remove:digitSum} and {pad:flag digitSum}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolDigitSum ( $x, $p=0 ) {

    return pqBoolWhole ( $x ) and $x >= 0;

  }

?>
