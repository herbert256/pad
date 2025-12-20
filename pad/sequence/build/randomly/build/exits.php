<?php

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
