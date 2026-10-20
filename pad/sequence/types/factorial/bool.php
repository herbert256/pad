<?php

  // Membership test for factorial, 1, 2, 6, 24, 120, ...: x is divided by 2, 3, 4, ... in turn
  // and must come to 1 before a division leaves a remainder - twenty steps at the most.
  // build/check.php asks it for {keep factorial}, {remove factorial} and {flag factorial}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolFactorial ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 1 )
      return FALSE;

    for ( $x = (int) $x, $i = 2; $x > 1; $i++ ) {

      if ( $x % $i )
        return FALSE;

      $x = intdiv ( $x, $i );

    }

    return TRUE;

  }

?>
