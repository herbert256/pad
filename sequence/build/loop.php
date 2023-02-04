<?php

  if ( isset($padPrm [$pad] ['from']) )
    $padSeqLoopStart = $padSeqFrom;
  else
    $padSeqLoopStart = 1; 
  
  if ( isset($padPrm [$pad] ['to']) )
    $padSeqLoopEnd = $padSeqTo;
  else 
    $padSeqLoopEnd = PHP_INT_MAX;

?>