<?php

  if ( isset($pad_prms_tag ['from']) )
    $pad_seq_loop_start = $pad_seq_from;
  else
    $pad_seq_loop_start = 1; 
  
  if ( isset($pad_prms_tag ['to']) )
    $pad_seq_loop_end = $pad_seq_to;
  else 
    $pad_seq_loop_end = PHP_INT_MAX;

?>