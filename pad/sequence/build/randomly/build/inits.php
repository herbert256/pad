<?php

  // Arms the two-pass fallback that makes randomly= work on a computed sequence.
  //
  // First half of the pair bracketing the strategy dispatch in build/build.php. Sampling
  // needs a term list to sample from, which only the store-style strategies have, so for a
  // function, bool or order build this saves from/increment/rows/plays, switches randomly
  // off and raises $pqRandomlyBuild: the build then runs in order and
  // build/randomly/build/exits.php does the sampling. When a to= was given the rows limit
  // is dropped for that first pass, so the whole requested window is generated before it
  // is sampled. Any other strategy is left alone, with $pqRandomlyBuild FALSE.

  $pqRandomlyBuild = FALSE;

  if ( ! $pqRandomly )                                        return;
  if ( ! in_array ( $pqBuild, ['function','bool','order'] ) ) return;

  $pqFromRandomly  = $pqFrom;
  $pqIncRandomly   = $pqInc;
  $pqPlaysRandomly = $pqPlays;
  $pqRowsRandomly  = $pqRows;

  if ( $pqTo !== PHP_INT_MAX )
    $pqRows = 0;

  $pqRandomly      = FALSE;
  $pqRandomlyBuild = TRUE;

?>