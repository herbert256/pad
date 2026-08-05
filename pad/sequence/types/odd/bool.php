<?php

  // Membership predicate for odd: pqBoolOdd($n) is TRUE when the low bit of n is set.
  //
  // Not the generation path - pqBuild() prefers this type's make.php - but the test
  // build/check.php uses for {keep odd}, {remove odd} and {flag odd}.

  function pqBoolOdd( $n, $p=0 ) {

    if ( $n & 1 )
      return TRUE;
    else
      return FALSE;

  }

?>