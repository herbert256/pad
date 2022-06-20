<?php

  if ( isset($pad_parms_tag ['from']) )
    $pad_seq_start = $pad_seq_from;
  else
    $pad_seq_start = 1; 
  
  if ( isset($pad_parms_tag ['to']) )
    $pad_seq_end = $pad_seq_to;
  else 
    $pad_seq_end = PHP_INT_MAX;

?>