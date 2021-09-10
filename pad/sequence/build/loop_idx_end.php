<?php

  if ( isset($pad_parms_tag ['from']) ) {
    $pad_seq_loop_idx = $pad_seq_from;
  } else {
    $pad_seq_loop_idx = 1; 
  }

  if ( isset($pad_parms_tag ['to']) ) {
    $pad_seq_loop_end = $pad_seq_to;
  } else {
    $pad_seq_loop_end = PHP_INT_MAX;
  }  

?>