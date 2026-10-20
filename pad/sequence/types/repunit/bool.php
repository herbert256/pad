<?php

  // Membership test for repunit, 0, 1, 11, 111, ...: a whole number written with ones only, or
  // 0. build/check.php asks it for {keep repunit}, {remove repunit} and {flag repunit}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolRepunit ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 0 )
      return FALSE;

    return trim ( (string) (int) $x, '1' ) === '' or $x == 0;

  }

?>
