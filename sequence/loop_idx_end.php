<?php

  if ( isset($pad_parms_tag ['from']) or isset($pad_parms_tag ['to']) ) {
    $pad_seq_loop_idx = $pad_seq_from;
    $pad_seq_loop_end = $pad_seq_to;
  } else {
    $pad_seq_loop_idx = 1; 
    $pad_seq_loop_end = PHP_INT_MAX;
  }

  $pad_seq_loop_idx = pad_seq_check_even_odd_up   ( $pad_seq_loop_idx, $pad_seq_even, $pad_seq_odd );
  $pad_seq_loop_idx = pad_seq_check_multiple_up   ( $pad_seq_loop_idx, $pad_seq_multiple );

  $pad_seq_loop_end = pad_seq_check_even_odd_down ( $pad_seq_loop_end, $pad_seq_even, $pad_seq_odd );
  $pad_seq_loop_end = pad_seq_check_multiple_down ( $pad_seq_loop_end, $pad_seq_multiple );

?>