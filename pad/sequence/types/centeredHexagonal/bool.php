<?php

  // Membership test for centeredHexagonal, 1, 7, 19, 37, 61, ...: x is a term when x - 1 is
  // 3n(n - 1) for a whole n, which stands next to the square root of (x - 1) / 3, so the root
  // is taken once and the whole numbers around it are tried exactly. build/check.php asks it
  // for {keep centeredHexagonal}, {remove centeredHexagonal} and {flag centeredHexagonal}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolCenteredHexagonal ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 1 or ( $x - 1 ) % 3 )
      return FALSE;

    $m    = intdiv ( (int) $x - 1, 3 );
    $root = (int) sqrt ( (float) $m );

    for ( $n = max ( 1, $root - 1 ); $n <= $root + 2; $n++ )
      if ( $n * ( $n - 1 ) == $m )
        return TRUE;

    return FALSE;

  }

?>
