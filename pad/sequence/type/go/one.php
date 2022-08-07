<?php

  $pSeq++;
  $pSeq_protectCnt++;

  $pSeq_one_done = [];

  if ( $pSeq_protectCnt > $pSeq_protect                ) return FALSE;
  if ( $pSeq < $pSeq_low                                ) return TRUE;
  if ( $pSeq > $pSeq_high                               ) return FALSE;
  if ( $pSeq_build == 'order' and $pSeq < $pSeq_from ) return TRUE;
  if ( $pSeq_build == 'order' and $pSeq > $pSeq_to   ) return FALSE;

  $pSequence = include 'sequence.php';

  if     ( $pSequence === TRUE  ) return TRUE;
  elseif ( $pSequence === FALSE ) return FALSE;   

  $pSequence = include 'operations.php';

  if     ( $pSequence === TRUE  ) return TRUE;
  elseif ( $pSequence === FALSE ) return FALSE;    

  if ( $pSeq_unique and in_array ($pSequence, $pSeq_result) ) return TRUE;
  if ( is_numeric($pSequence) and $pSequence > PHP_INT_MAX     ) return FALSE; 

  $pSeq_base++;

  if ( is_numeric($pSequence) and $pSequence < $pSeq_min ) return TRUE;  
  if ( is_numeric($pSequence) and $pSequence > $pSeq_max ) return TRUE; 
  if ( $pSeq_page  and $pSeq_base < $pSeq_page_start     ) return TRUE; 
  if ( $pSeq_start and $pSeq_base < $pSeq_start          ) return TRUE;

  $pSeq_result [] = $pSequence;

  $pSeq_protectCnt = 0;

  if ( $pSeq_rows and count($pSeq_result) >= $pSeq_rows ) return FALSE;
  if ( $pSeq_end  and $pSeq_base          >= $pSeq_end  ) return FALSE;

  return TRUE;

?>