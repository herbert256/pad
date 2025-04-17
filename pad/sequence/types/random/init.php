<?php

  $pqRandomStart = $pqMin;
  $pqRandomEnd   = $pqMax;
  $pqRandomStep  = $padPrm [$pad] ['step'] ?? 1;

  $pqDone [] = 'step'; 

  if ( $pqRandomStep <> 1 )
    $pqRandomSteps = intval ( ( $pqRandomEnd - $pqRandomStart ) / $pqRandomStep );
  
?>