<?php

  if ( isset($padPrm [$pad] ['from']) )
    $padSeqStart = $padSeqFrom;
  else
    $padSeqStart = 1; 
  
  if ( isset($padPrm [$pad] ['to']) )
    $padSeqEnd = $padSeqTo;
  else 
    $padSeqEnd = PHP_INT_MAX;

?>