<?php

  // Second pass of the randomly= fallback: samples the terms the ordered build produced.
  //
  // Runs after the strategy dispatch in build/build.php and does nothing unless
  // build/randomly/build/inits.php raised $pqRandomlyBuild. Moves $pqResult into $pqFixed,
  // restores the saved rows/plays, switches to a pull-style fixed build
  // with randomly back on, and re-runs the fixed iterator - so the answer is a random
  // selection out of the terms just generated.

  if ( ! $pqRandomlyBuild )
    return;

  $pqFixed  = $pqResult;
  $pqResult = [];

  $pqSeq      = '';
  $pqBuild    = 'pull';
  $pqRandomly = TRUE;

  // The ordered pass has applied the window already - from, to, skip and the increment
  // shaped the list now in $pqFixed - so the sampling pass takes that list whole. Applied a
  // second time, from= was read as a position in the list, so {sequence happy, from=10,
  // to=50, randomly} sampled from its tenth term on and found none, and skip= and the
  // increment struck twice.

  $pqFrom  = 1;
  $pqTo    = PHP_INT_MAX;
  $pqSkip  = 0;
  $pqInc   = 1;
  $pqRows  = $pqRowsRandomly;
  $pqPlays = $pqPlaysRandomly;

  include PQ . 'build/types/type/fixed.php';

?>