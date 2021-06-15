<?php

  $pad_seq_rand = round ( $pad_seq_rand / $pad_seq_multiple ) * $pad_seq_multiple;

  if ( $pad_seq_rand < $pad_seq_loop_idx ) $pad_seq_rand = $pad_seq_rand + $pad_seq_multiple;
  if ( $pad_seq_rand > $pad_seq_loop_end ) $pad_seq_rand = $pad_seq_rand - $pad_seq_multiple;

  return $pad_seq_rand;

?>