<?php

  // Membership test for the biquadratic sequence, 1, 16, 81, 256, ...: n^4 for a whole n from
  // 0 up, decided from the fourth root - the whole n next to it are tried exactly. build/
  // check.php asks it for {keep biquadratic}, {remove biquadratic} and {flag biquadratic}.
  // Without it the check read the 10,000 terms of the PADbiquadratic table and generated on
  // past them.
  //
  // Not the type's build: pqBuild() ranks loop.php before bool.php.

  function pqBoolBiquadratic ( $n, $p=0 ) {

    // 0 is the term at position 0, which the type makes - turned down, the run made a 0 its
    // own test denied.

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    if ( $n == 0 )
      return TRUE;

    $n    = (int) $n;
    $root = (int) round ( sqrt ( sqrt ( (float) $n ) ) );

    for ( $i = max ( 1, $root - 1 ); $i <= $root + 1; $i++ )
      if ( $i ** 4 == $n )
        return TRUE;

    return FALSE;

  }

?>
