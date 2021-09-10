<?php

  $pad_seq_jump = $pad_seq_multiple;

  if ( $pad_seq_multiple > 1 and ($check % $pad_seq_multiple) )
    $pad_seq_loop_idx = ceil ( $pad_seq_loop_idx / $pad_seq_multiple ) * $pad_seq_multiple;

  $pad_seq_prepare [0] = $pad_seq_loop_idx;

?>