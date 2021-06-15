<?php

  if     ( $pad_seq_type == 'from' ) $pad_seq_loop_idx = ($pad_seq_from * 2) - 1;
  elseif ( $pad_seq_type == 'min'  ) $pad_seq_loop_idx = $pad_seq_min;

  if ( $pad_seq_loop_idx % 2 )
    return $pad_seq_loop_idx;
  else
    return $pad_seq_loop_idx + 1;

?>