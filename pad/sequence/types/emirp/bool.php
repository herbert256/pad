<?php

  // Membership test for the emirp sequence, 13, 17, 31, 37, 71, ...: a prime whose digits
  // reversed make another prime - the test loop.php makes of every candidate, kept here as a
  // predicate. build/check.php asks it for {keep emirp}, {remove emirp} and {flag emirp}.
  // Without it the check read the PADemirp table, the emirps below 10,000, and generated on
  // past it for every larger candidate.
  //
  // Not the type's build: pqBuild() ranks loop.php before bool.php.

  include_once PT . 'prime/bool.php';

  function pqBoolEmirp ( $n, $p=0 ) {

    if ( ! pqBoolPrime ( $n ) )
      return FALSE;

    $reverse = padTypeReverse ( (int) $n );

    return ( $reverse != $n and pqBoolPrime ( $reverse ) );

  }

?>
