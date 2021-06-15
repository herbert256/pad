<?php

  if ( $pad_seq_type == 'from' )
    $pad_seq_loop_idx = intval($pad_seq_from) * $pad_seq_step;

  return $pad_seq_loop_idx;

?>