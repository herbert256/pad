<?php

  // Membership test for the even sequence: pqBoolEven() is TRUE when the low bit of n is
  // clear. Not the type's own build strategy - it has no loop.php, so pqBuild() picks
  // make.php - but the predicate build/check.php calls for {keep even}, {remove even} and
  // {flag even}, and loaded for every even build by build/include.php.

  function pqBoolEven( $n, $p=0 ) {

    if ( $n & 1 )
      return FALSE;
    else
      return TRUE;

  }

?>