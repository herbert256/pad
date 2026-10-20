<?php

  // Membership test for divisors: every whole number from 1 up is how many divisors some n
  // has - 2^(k-1) has k of them - so that is the whole test. build/check.php asks it for
  // {keep divisors}, {remove divisors} and {flag divisors}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolDivisors ( $x, $p=0 ) {

    return pqBoolWhole ( $x ) and $x >= 1;

  }

?>
