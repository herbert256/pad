<?php

  if ( isset($padPrm [$pad] ['from']) )
    $padSeqLoopStart = $padSeqFrom;
  else
    $padSeqLoopStart = 1; 
  
  if ( isset($padPrm [$pad] ['to']) )
    $padSeqLoopEnd = $padSeqTo;
  else 
    $padSeqLoopEnd = PHP_INT_MAX;

  if ( $padSeqInc == 1 and isset ( $padPrm [$pad] ['step'] ) and $padSeqSeq <> 'step' )
    $padSeqInc = intval ( $padPrm [$pad] ['step'] );

  if ( $padSeqInc == 1 and isset ( $padPrm [$pad] ['multiple'] ) and $padSeqSeq <> 'multiple' ) {
    $padSeqInc = intval ( $padPrm [$pad] ['multiple'] );
    $padSeqLoopStart = ceil ( $padSeqLoopStart / $padSeqInc) * $padSeqInc;
  }

?>