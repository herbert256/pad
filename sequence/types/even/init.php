<?php

  if ( $pad_seq_type == 'from' )
    $pad_seq_loop_idx = intval($pad_seq_from) * 2;

  if ( $pad_seq_loop_idx % 2 )
    return $pad_seq_loop_idx + 1;
  else
    return $pad_seq_loop_idx;

?>