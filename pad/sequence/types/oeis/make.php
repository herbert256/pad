<?php

  // Make build for oeis, the strategy pqBuild() picks for this type: returns the $pqLoop'th
  // term of the OEIS sequence named by the parameter, or FALSE once the table runs out,
  // which ends the build.
  //
  // Same generated table as oeis/fixed.php, read a term at a time, so from/to/increment
  // index into the sequence. The table is 0-indexed, hence $pqLoop-1.

  include_once PT . 'oeis/oeis.php';

  if ( isset ( OEIS [$pqParm] [$pqLoop-1] ) )
    return OEIS [$pqParm] [$pqLoop-1];
  else
    return FALSE;

?>