<?php

  // Membership test for the tetrahedral sequence, 1, 4, 10, 20, 35, ...: n(n + 1)(n + 2)/6
  // for a whole n from 1 up. That is about (n + 1)^3 / 6, so the cube root of 6x gives n
  // give or take one, and the whole n next to it are tried with the exact pqProduct the type
  // generates with. build/check.php asks it for {keep tetrahedral}, {remove tetrahedral}
  // and {flag tetrahedral}; without it the check generated the sequence up to the
  // candidate, stopped at the million-candidate ceiling and answered no past it.

  function pqBoolTetrahedral ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n    = (int) $n;
    $root = (int) ( ( 6 * (float) $n ) ** ( 1 / 3 ) ) - 1;

    for ( $i = max ( 1, $root - 1 ); $i <= $root + 2; $i++ )
      if ( pqProduct ( [ $i, $i + 1, $i + 2 ], 6 ) == $n )
        return TRUE;

    return FALSE;

  }

?>
