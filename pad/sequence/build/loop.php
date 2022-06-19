<?php

  if ( isset($pad_parms_tag ['from']) )
    $pad_seq_start = $pad_seq_from;
  else
    $pad_seq_start = 1; 
  
  if ( isset($pad_parms_tag ['to']) )
    $pad_seq_end = $pad_seq_to;
  else 
    $pad_seq_end = PHP_INT_MAX;

  $pad_seq_max       = ($pad_seq_end - $pad_seq_start) + 1;
  $pad_seq_increment = $pad_seq_increment; 

?>