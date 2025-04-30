<?php

  $pqRandomlyBuild = FALSE;

  if ( ! $pqRandomly )                                        return;
  if ( ! in_array ( $pqBuild, ['function','bool','order'] ) ) return;

  $pqFromSave      = $pqFrom;
  $pqIncSave       = $pqInc;
  $pqPlaysSave     = $pqPlays;
  $pqRowsSave      = $pqRows;

  if ( $pqTo !== PHP_INT_MAX ) 
    $pqRows = 0;

  $pqRandomly      = FALSE;
  $pqRandomlyBuild = TRUE;
 
?>