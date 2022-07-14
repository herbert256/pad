<?php

  $pad_seq++;
  $pad_seq_protect_cnt++;

  $pad_seq_one_done = [];

  if ( $pad_seq_protect_cnt > $pad_seq_protect                ) return FALSE;
  if ( $pad_seq < $pad_seq_low                                ) return TRUE;
  if ( $pad_seq > $pad_seq_high                               ) return FALSE;
  if ( $pad_seq_build == 'order' and $pad_seq < $pad_seq_from ) return TRUE;
  if ( $pad_seq_build == 'order' and $pad_seq > $pad_seq_to   ) return FALSE;

  $pad_sequence = include 'sequence.php';

  if     ( $pad_sequence === TRUE  ) return TRUE;
  elseif ( $pad_sequence === FALSE ) return FALSE;   

  $pad_sequence = include 'operations.php';

  if     ( $pad_sequence === TRUE  ) return TRUE;
  elseif ( $pad_sequence === FALSE ) return FALSE;    

  if ( $pad_seq_unique and in_array ($pad_sequence, $pad_seq_result) ) return TRUE;
  if ( is_numeric($pad_sequence) and $pad_sequence > PHP_INT_MAX     ) return FALSE; 

  $pad_seq_base++;

  if ( is_numeric($pad_sequence) and $pad_sequence < $pad_seq_min ) return TRUE;  
  if ( is_numeric($pad_sequence) and $pad_sequence > $pad_seq_max ) return TRUE; 
  if ( $pad_seq_page  and $pad_seq_base < $pad_seq_page_start     ) return TRUE; 
  if ( $pad_seq_start and $pad_seq_base < $pad_seq_start          ) return TRUE;

  $pad_seq_result [] = $pad_sequence;

  $pad_seq_protect_cnt = 0;

  if ( $pad_seq_rows and count($pad_seq_result) >= $pad_seq_rows ) return FALSE;
  if ( $pad_seq_end  and $pad_seq_base          >= $pad_seq_end  ) return FALSE;

  return TRUE;

?>