<?php

  // Second pass of the randomly= fallback: samples the terms the ordered build produced.
  //
  // Runs after the strategy dispatch in build/build.php and does nothing unless
  // build/randomly/build/inits.php raised $pqRandomlyBuild. Moves $pqResult into $pqFixed,
  // restores the saved from/increment/rows/plays, switches to a pull-style fixed build
  // with randomly back on, and re-runs the fixed iterator - so the answer is a random
  // selection out of the terms just generated.

  if ( ! $pqRandomlyBuild )
    return;

  $pqFixed  = $pqResult;
  $pqResult = [];

  $pqSeq      = '';
  $pqBuild    = 'pull';
  $pqRandomly = TRUE;

  $pqFrom  = $pqFromRandomly;
  $pqInc   = $pqIncRandomly;
  $pqRows  = $pqRowsRandomly;
  $pqPlays = $pqPlaysRandomly;

  include PQ . 'build/types/type/fixed.php';

?>