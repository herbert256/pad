<?php

  $padSeqRandomStart = $padSeqMin;
  $padSeqRandomEnd   = $padSeqMax;
  $padSeqRandomStep  = $padPrm [$pad] ['step'] ?? 1;

  $padSeqDone [] = 'step'; 

  if ( $padSeqRandomStep <> 1 )
    $padSeqRandomSteps = intval ( ( $padSeqRandomEnd - $padSeqRandomStart ) / $padSeqRandomStep );
  
?>