<?php

  $pqRandomStart = ( $pqMin == PHP_INT_MIN ) ? 1 : $pqMin;
  $pqRandomEnd   = $pqMax;
  $pqRandomStep  = $padPrm [$pad] ['step'] ?? 1;

  if ( $pqRandomStep <> 1 )
    $pqRandomSteps = intval ( ( $pqRandomEnd - $pqRandomStart ) / $pqRandomStep );
  
?>