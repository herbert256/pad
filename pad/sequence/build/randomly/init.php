<?php

  // Works out the window random picks are drawn from, before either iterator starts.
  //
  // Included first by build/types/type/loop.php and .../fixed.php; does nothing unless
  // randomly= is on. Stores the from/to bounds and the number of increment-sized steps
  // between them as $pqRandomlyStart/$pqRandomlyEnd/$pqRandomlySteps, which
  // build/randomly/randomly.php then draws from. For a stored sequence the window indexes
  // terms rather than naming values, so the upper bound is clamped to the last index of
  // $pqFixed and the lower one steps back off the 1-based from= to the 0-based index it
  // stands for - without that the first term of a store could never be drawn.

  if ( ! $pqRandomly )
    return;

  $pqRandomlyStart = $pqFrom;
  $pqRandomlyEnd   = $pqTo;

  if ( pqStore ( $pqBuild ) ) {

    $pqRandomlyStart = $pqFrom - 1;

    if ( $pqRandomlyEnd > count ( $pqFixed ) - 1 )
      $pqRandomlyEnd = count ( $pqFixed ) - 1;

  }

  $pqRandomlySteps = intval ( ( $pqRandomlyEnd - $pqRandomlyStart ) / $pqInc );

?>