<?php

  // Works out the window random picks are drawn from, before either iterator starts.
  //
  // Included first by build/types/type/loop.php and .../fixed.php; does nothing unless
  // randomly= is on. Stores the from/to bounds and the number of increment-sized steps
  // between them as $pqRandomlyStart/$pqRandomlyEnd/$pqRandomlySteps, which
  // build/randomly/randomly.php then draws from. For a stored sequence the upper bound is
  // clamped to the last index of $pqFixed, since there the window indexes terms.

  if ( ! $pqRandomly )
    return;

  $pqRandomlyStart = $pqFrom;
  $pqRandomlyEnd   = $pqTo;

  if ( pqStore ( $pqBuild ) )
    if ( $pqRandomlyEnd > count ( $pqFixed ) - 1 )
      $pqRandomlyEnd = count ( $pqFixed ) - 1;

  $pqRandomlySteps = intval ( ( $pqRandomlyEnd - $pqRandomlyStart ) / $pqInc );

?>