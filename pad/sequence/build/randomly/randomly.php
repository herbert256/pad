<?php

  // Draws one random candidate from the window set up by build/randomly/init.php.
  //
  // Included as an expression by build/one.php when randomly= is on, and returns the value
  // to use as $pqLoop: for a stored sequence the term at a random index of $pqFixed,
  // otherwise a random point on the from/to/increment grid.

  // Under unique every step of the window is drawn once before any is drawn again - see
  // build/randomly/init.php.

  if ( $pqRandomlyOnce and $pqRandomlyDrawn <= $pqRandomlySteps ) {

    $pqRandomlyPick = rand ( $pqRandomlyDrawn, $pqRandomlySteps );
    $pqRandomlyStep = $pqRandomlySwap [$pqRandomlyPick] ?? $pqRandomlyPick;

    $pqRandomlySwap [$pqRandomlyPick] = $pqRandomlySwap [$pqRandomlyDrawn] ?? $pqRandomlyDrawn;
    unset ( $pqRandomlySwap [$pqRandomlyDrawn] );

    $pqRandomlyDrawn++;

  } else

    $pqRandomlyStep = rand ( 0, $pqRandomlySteps );

  $pqRandomlyRand = $pqRandomlyStart + $pqRandomlyStep * $pqRandomlyInc;

  if ( pqStore ( $pqBuild ) )
    return $pqFixed [$pqRandomlyRand];
  else
    return $pqRandomlyRand;

?>
