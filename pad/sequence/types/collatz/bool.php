<?php

  // Membership test for collatz: every whole number k from 0 up is a step count - 2^k takes k
  // halvings - so that is the whole test. build/check.php asks it for {keep collatz}, {remove
  // collatz} and {flag collatz}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolCollatz ( $x, $p=0 ) {

    return pqBoolWhole ( $x ) and $x >= 0;

  }

?>
