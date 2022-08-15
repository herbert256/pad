<?php

  if ( isset($padPrmsTag [$pad] ['from']) )
    $padSeqLoopStart = $padSeqFrom;
  else
    $padSeqLoopStart = 1; 
  
  if ( isset($padPrmsTag [$pad] ['to']) )
    $padSeqLoopEnd = $padSeqTo;
  else 
    $padSeqLoopEnd = PHP_INT_MAX;

?>