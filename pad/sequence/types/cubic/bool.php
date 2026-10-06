<?php

  // Membership test for the cubic sequence, 1, 8, 27, 64, ...: n^3 for a whole n from 1 up,
  // decided from the cube root - the whole n next to it are tried exactly. build/check.php
  // asks it for {keep cubic}, {remove cubic} and {flag cubic}; without it the check
  // generated the cubes up to the candidate, stopped at the million-candidate ceiling and
  // answered no past it - 8 * 10^18, the cube of 2000000, was no cube.

  function pqBoolCubic ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n    = (int) $n;
    $root = (int) round ( $n ** ( 1 / 3 ) );

    for ( $i = max ( 1, $root - 1 ); $i <= $root + 1; $i++ )
      if ( $i ** 3 == $n )
        return TRUE;

    return FALSE;

  }

?>
