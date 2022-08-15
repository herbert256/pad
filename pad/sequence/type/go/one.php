<?php

  $padSeq++;
  $padSeq_protectCnt++;

  $padSeq_one_done = [];

  if ( $padSeq_protectCnt > $padSeq_protect                ) return FALSE;
  if ( $padSeq < $padSeq_low                                ) return TRUE;
  if ( $padSeq > $padSeq_high                               ) return FALSE;
  if ( $padSeq_build == 'order' and $padSeq < $padSeq_from ) return TRUE;
  if ( $padSeq_build == 'order' and $padSeq > $padSeq_to   ) return FALSE;

  $padSequence = include 'sequence.php';

  if     ( $padSequence === TRUE  ) return TRUE;
  elseif ( $padSequence === FALSE ) return FALSE;   

  $padSequence = include 'operations.php';

  if     ( $padSequence === TRUE  ) return TRUE;
  elseif ( $padSequence === FALSE ) return FALSE;    

  if ( $padSeq_unique and in_array ($padSequence, $padSeq_result) ) return TRUE;
  if ( is_numeric($padSequence) and $padSequence > PHP_INT_MAX     ) return FALSE; 

  $padSeq_base++;

  if ( is_numeric($padSequence) and $padSequence < $padSeq_min ) return TRUE;  
  if ( is_numeric($padSequence) and $padSequence > $padSeq_max ) return TRUE; 
  if ( $padSeq_page  and $padSeq_base < $padSeq_page_start     ) return TRUE; 
  if ( $padSeq_start and $padSeq_base < $padSeq_start          ) return TRUE;

  $padSeq_result [] = $padSequence;

  $padSeq_protectCnt = 0;

  if ( $padSeq_rows and count($padSeq_result) >= $padSeq_rows ) return FALSE;
  if ( $padSeq_end  and $padSeq_base          >= $padSeq_end  ) return FALSE;

  return TRUE;

?>