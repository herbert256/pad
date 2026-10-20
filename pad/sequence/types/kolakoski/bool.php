<?php

  // Membership test for the kolakoski sequence, 1, 2, 2, 1, 1, 2, ...: it holds the values 1
  // and 2 and no other. build/check.php asks it for {keep kolakoski}, {remove kolakoski} and
  // {flag kolakoski}. Without it the check looked in the 55,893 terms of fixed.php, an index
  // of them made for every request that asked.
  //
  // Not the type's build: pqBuild() ranks fixed.php before bool.php.

  function pqBoolKolakoski ( $n, $p=0 ) {

    return pqBoolWhole ( $n ) and ( $n == 1 or $n == 2 );

  }

?>
