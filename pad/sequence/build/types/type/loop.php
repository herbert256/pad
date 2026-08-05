<?php

  // The loop iterator: counts from $pqFrom to $pqTo in steps of $pqInc and offers each
  // value to build/one.php as a candidate.
  //
  // Shared by every computed strategy - loop, make, bool, function, check and order.
  // Stops as soon as build/one.php returns FALSE: rows= filled, the stop= value reached,
  // or the try limit exhausted. A random increment (increment=a...b) is re-rolled after
  // every candidate.

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