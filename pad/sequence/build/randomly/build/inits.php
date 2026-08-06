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
  //
  // The plays are put aside rather than merely copied: the second pass restores them and
  // runs them over what it samples, so leaving them in place here applied each of them
  // twice - a make=negation came back positive.

  $pqRandomlyBuild = FALSE;

  if ( ! $pqRandomly )                                        return;
  if ( ! in_array ( $pqBuild, ['function','bool','order'] ) ) return;

  $pqFromRandomly  = $pqFrom;
  $pqIncRandomly   = $pqInc;
  $pqPlaysRandomly = $pqPlays;
  $pqRowsRandomly  = $pqRows;

  $pqPlays         = [];

  if ( $pqTo !== PHP_INT_MAX )
    $pqRows = 0;

  $pqRandomly      = FALSE;
  $pqRandomlyBuild = TRUE;

?>