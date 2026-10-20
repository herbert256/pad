<?php

  // Membership test for the newmanConway sequence, 1, 1, 2, 2, 3, 4, 4, 4, 5, ...: each term
  // is the one before it or one more, so from its first term, 1, it passes every whole number
  // and nothing else. build/check.php asks it for {keep newmanConway}, {remove newmanConway}
  // and {flag newmanConway}. Without it the check read the 10,000 terms of the
  // PADnewmanConway table and generated on past its largest term, 5373.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolNewmanConway ( $n, $p=0 ) {

    return pqBoolWhole ( $n ) and $n >= 1;

  }

?>
