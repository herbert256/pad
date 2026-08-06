<?php

  // The loop iterator: counts from $pqFrom to $pqTo in steps of $pqInc and offers each
  // value to build/one.php as a candidate.
  //
  // Shared by every computed strategy - loop, make, bool, function, check and order.
  // Stops as soon as build/one.php returns FALSE: rows= filled, the stop= value reached,
  // or the try limit exhausted. A random increment (increment=a...b) is re-rolled after
  // every candidate.
  //
  // An increment below 1 never advances the counter - 0 leaves it where it is and a
  // negative one walks away from the from <= to condition - so the walk would not end.
  // The run returns FALSE instead of starting it, and the tag takes its else branch.

  if ( $pqInc < 1 )
    return FALSE;

  include PQ . 'build/randomly/init.php';

  $pqGo = $pqFrom;

  while ( $pqGo <= $pqTo ) {

    $pqLoop = $pqGo;

    if ( ! include PQ . 'build/one.php')
      break;

    if ( $pqRandomInc )
      $pqInc = pqRandomParm3 ( $pqRandomInc );

    $pqGo = $pqGo + $pqInc;

  }

?>