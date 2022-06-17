<?php

  $pad_seq_random = TRUE;

  if ( isset($pad_parms_tag ['from']) )
    $pad_seq_random_start = $pad_seq_from;
  else
    $pad_seq_random_start = 1; 
  
  if ( isset($pad_parms_tag ['to']) )
    $pad_seq_random_end = $pad_seq_to;
  else 
    $pad_seq_random_end = PHP_INT_MAX;

  if ( $pad_seq_min > $pad_seq_random_start )
    $pad_seq_random_start = $pad_seq_min;

  if ( $pad_seq_max < $pad_seq_random_end )
    $pad_seq_random_end = $pad_seq_max;

?>