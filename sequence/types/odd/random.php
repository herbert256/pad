<?php

  if ( ! ($pad_seq_rand % 2) )
    $pad_seq_rand++;

  if ( $pad_seq_rand < $pad_seq_loop_idx ) $pad_seq_rand = $pad_seq_rand + 2;
  if ( $pad_seq_rand > $pad_seq_loop_end ) $pad_seq_rand = $pad_seq_rand - 2;

  return $pad_seq_rand;

?>