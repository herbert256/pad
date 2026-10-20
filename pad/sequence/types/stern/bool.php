<?php

  // Membership test for stern: every whole number from 0 up is a term - k is a(2^k - 1) - so
  // that is the whole test. build/check.php asks it for {keep stern}, {remove stern} and {flag
  // stern}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolStern ( $x, $p=0 ) {

    return pqBoolWhole ( $x ) and $x >= 0;

  }

?>
