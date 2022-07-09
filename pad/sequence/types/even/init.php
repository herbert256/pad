<?php

  $pad_seq_inc = 2;

  $pad_seq_loop_start = $pad_seq_from * 2;

  if ( $pad_seq_to <> PHP_INT_MAX )
    $pad_seq_loop_end = $pad_seq_to * 2;

  if ( $pad_seq_loop_start % 2 )
    $pad_seq_loop_start++;

?>