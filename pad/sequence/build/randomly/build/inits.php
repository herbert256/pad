<?php

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