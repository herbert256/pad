<?php

  // Membership test for the octahedral sequence, 1, 6, 19, 44, 85, ...: n(2n^2 + 1)/3 for a
  // whole n from 1 up. That is about 2n^3 / 3, so the cube root of 3x/2 gives n give or take
  // one, and the whole n next to it are tried with the exact pqProduct the type generates
  // with. build/check.php asks it for {keep octahedral}, {remove octahedral} and {flag
  // octahedral}; without it the check generated the sequence up to the candidate, stopped at
  // the million-candidate ceiling and answered no past it. It replaces xbool.php, a disabled
  // predicate of the same name that rendered the sequence through padCode to search it.

  function pqBoolOctahedral ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n    = (int) $n;
    $root = (int) ( ( 1.5 * $n ) ** ( 1 / 3 ) );

    for ( $i = max ( 1, $root - 1 ); $i <= $root + 1; $i++ )
      if ( pqProduct ( [ $i, 2 * $i * $i + 1 ], 3 ) == $n )
        return TRUE;

    return FALSE;

  }

?>
