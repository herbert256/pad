<?php

  // Membership test for the cubic sequence, 1, 8, 27, 64, ...: n^3 for a whole n from 0 up,
  // decided from the cube root - the whole n next to it are tried exactly. build/check.php
  // asks it for {keep cubic}, {remove cubic} and {flag cubic}; without it the check
  // generated the cubes up to the candidate, stopped at the million-candidate ceiling and
  // answered no past it - 8 * 10^18, the cube of 2000000, was no cube.

  function pqBoolCubic ( $n, $p=0 ) {

    // 0 is the term at position 0, which the type makes - turned down, the run made a 0 its
    // own test denied.

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    if ( $n == 0 )
      return TRUE;

    $n    = (int) $n;
    $root = (int) round ( $n ** ( 1 / 3 ) );

    for ( $i = max ( 1, $root - 1 ); $i <= $root + 1; $i++ )
      // A cube past the integer range is a float that compares equal to the integers just below
      // 2^63 - 2097152 ** 3 made 9223372036854775807 a cube - and is no term: the run ends at it.
      if ( is_int ( $i ** 3 ) and $i ** 3 == $n )
        return TRUE;

    return FALSE;

  }

?>
