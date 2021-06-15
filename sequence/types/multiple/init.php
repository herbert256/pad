<?php

  if ( $pad_seq_type == 'from' )
    $pad_seq_loop_idx = intval($pad_seq_from) * $pad_seq_multiple;

  return ceil ( $pad_seq_loop_idx / $pad_seq_multiple ) * $pad_seq_multiple;

?>