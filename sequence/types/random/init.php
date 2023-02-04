<?php

  $padSeqRandom = TRUE;

  $padSeqRandomStart = $padSeqLoopStart;
  $padSeqRandomEnd   = $padSeqLoopEnd;

  if ( $padSeqMin and $padSeqFrom == 1 )
    $padSeqRandomStart = $padSeqMin;
  
  if ( $padSeqMax <> PHP_INT_MAX and $padSeqTo == PHP_INT_MAX )
    $padSeqRandomEnd = $padSeqMax;
  
  if ( $padSeqLoopStart == 1 and $padSeqLoopEnd == PHP_INT_MAX and ! $padSeqRows ) {
    $padSeqRows = ($padSeqMax - $padSeqMin) + 1;
    $padSeqLoopEnd  = $padSeqLoopStart + $padSeqRows;
  }

?>