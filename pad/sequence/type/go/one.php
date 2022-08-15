<?php

  $padSeq++;
  $padSeqProtectCnt++;

  $padSeqOneDone = [];

  if ( $padSeqProtectCnt > $padSeqProtect                ) return FALSE;
  if ( $padSeq < $padSeqLow                                ) return TRUE;
  if ( $padSeq > $padSeqHigh                               ) return FALSE;
  if ( $padSeqBuild == 'order' and $padSeq < $padSeqFrom ) return TRUE;
  if ( $padSeqBuild == 'order' and $padSeq > $padSeqTo   ) return FALSE;

  $padSequence = include 'sequence.php';

  if     ( $padSequence === TRUE  ) return TRUE;
  elseif ( $padSequence === FALSE ) return FALSE;   

  $padSequence = include 'operations.php';

  if     ( $padSequence === TRUE  ) return TRUE;
  elseif ( $padSequence === FALSE ) return FALSE;    

  if ( $padSeqUnique and in_array ($padSequence, $padSeqResult) ) return TRUE;
  if ( is_numeric($padSequence) and $padSequence > PHP_INT_MAX     ) return FALSE; 

  $padSeqBase++;

  if ( is_numeric($padSequence) and $padSequence < $padSeqMin ) return TRUE;  
  if ( is_numeric($padSequence) and $padSequence > $padSeqMax ) return TRUE; 
  if ( $padSeqPage  and $padSeqBase < $padSeqPageStart     ) return TRUE; 
  if ( $padSeqStart and $padSeqBase < $padSeqStart          ) return TRUE;

  $padSeqResult [] = $padSequence;

  $padSeqProtectCnt = 0;

  if ( $padSeqRows and count($padSeqResult) >= $padSeqRows ) return FALSE;
  if ( $padSeqEnd  and $padSeqBase          >= $padSeqEnd  ) return FALSE;

  return TRUE;

?>