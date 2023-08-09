<?php

  $padSeq++;
  $padSeqProtectCnt++;

  $padSeqOneDone = [];

  if ( $padSeqStopNext                                   ) return FALSE;
  if ( $padSeqProtectCnt > $padSeqProtect                ) return FALSE;
  if ( $padSeqBuild == 'order' and $padSeq < $padSeqFrom ) return TRUE;
  if ( $padSeqBuild == 'order' and $padSeq > $padSeqTo   ) return FALSE;

  $padSequence = include pad . 'sequence/type/go/sequence.php';

  if     ( $padSequence === TRUE  ) return TRUE;
  elseif ( $padSequence === FALSE ) return FALSE;   

  $padSequence = include pad . 'sequence/type/go/operations.php';

  if     ( $padSequence === TRUE  ) return TRUE;
  elseif ( $padSequence === FALSE ) return FALSE;    

  if ( $padSeqUnique and in_array ($padSequence, $padSeqResult) ) 
    return TRUE;

  $padSeqBase++;

  if ( $padSeqPage and $padSeqBase < $padSeqPageStart ) 
    return TRUE; 

  $padSeqResult [] = $padSequence;

  $padSeqProtectCnt = 0;

  if ( $padSeqRows and count($padSeqResult) >= $padSeqRows ) 
    return FALSE;

  return TRUE;

?>