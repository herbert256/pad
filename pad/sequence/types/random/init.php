<?php

  $pad_seq_random = TRUE;

  $pad_seq_random_start = $pad_seq_loop_start;
  $pad_seq_random_end   = $pad_seq_loop_end;

  if ( $pad_seq_min and $pad_seq_from == 1 )
    $pad_seq_random_start = $pad_seq_min;
  
  if ( $pad_seq_max <> PHP_INT_MAX and $pad_seq_to == PHP_INT_MAX )
    $pad_seq_random_end = $pad_seq_max;
  
  if ( $pad_seq_loop_start == 1 and $pad_seq_loop_end == PHP_INT_MAX and ! $pad_seq_rows ) {
    $pad_seq_rows = ($pad_seq_max - $pad_seq_min) + 1;
    $pad_seq_loop_end  = $pad_seq_loop_start + $pad_seq_rows;
  }

?>