<?php

  // median - reduces the sequence to its middle entry by position: entry number
  // count/2 rounded down, so an even-length sequence gives the upper of the two middles.
  // It picks positionally out of the pre-action snapshot $pqActionStart and does not sort
  // first, so the value is the true median only when the sequence is already in order.

  $pqTmp    = count ( $pqResult ) / 2;
  $pqMedian = (int) $pqTmp;

  $pqI = 0;

  foreach ( $pqActionStart as $padK => $padV ) {
    if ( $pqI == $pqMedian )
       $pqResult = [ $padK => $padV ];
    $pqI++;
  }

?>