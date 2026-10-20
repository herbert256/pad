<?php

  // Membership test for star, 1, 13, 37, 73, 121, ...: x is a term when x - 1 is 6n(n - 1)
  // for a whole n, which stands next to the square root of (x - 1) / 6, so the root is taken
  // once and the whole numbers around it are tried exactly. build/check.php asks it for {keep
  // star}, {remove star} and {flag star}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  function pqBoolStar ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 1 or ( $x - 1 ) % 6 )
      return FALSE;

    $m    = intdiv ( (int) $x - 1, 6 );
    $root = (int) sqrt ( (float) $m );

    for ( $n = max ( 1, $root - 1 ); $n <= $root + 2; $n++ )
      if ( $n * ( $n - 1 ) == $m )
        return TRUE;

    return FALSE;

  }

?>
