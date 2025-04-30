<?php

  if ( ! $pqRandomlyBuild )
    return;

  $pqFixed  = $pqResult;
  $pqResult = [];

  $pqSeq      = '';
  $pqBuild    = 'pull';
  $pqRandomly = TRUE;

  $pqFrom  = $pqFromSave;
  $pqInc   = $pqIncSave;
  $pqRows  = $pqRowsSave;
  $pqPlays = $pqPlays;

  include 'sequence/build/types/type/fixed.php';

?>