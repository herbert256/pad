<?php

  $padSeqRandom = TRUE;

  $padSeqRandomStart = $padSeqLoopStart;
  $padSeqRandomEnd   = $padSeqLoopEnd;

  if ( $padSeqLoopStart == 1 and $padSeqLoopEnd == PHP_INT_MAX and ! $padSeqRows ) {
    $padSeqRows = ($padSeqTo - $padSeqFrom) + 1;
    $padSeqLoopEnd  = $padSeqLoopStart + $padSeqRows;
  }

?>