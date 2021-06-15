<?php

  $pad_seq_rand  = $pad_seq_loop_end - $pad_seq_rand;
  $pad_seq_steps = round ( $pad_seq_rand / $pad_seq_step );
  $pad_seq_rand  = $pad_seq_loop_idx + ( $pad_seq_steps * $pad_seq_step );

  if ( $pad_seq_rand < $pad_seq_loop_idx ) $pad_seq_rand = $pad_seq_rand + $pad_seq_step;
  if ( $pad_seq_rand > $pad_seq_loop_end ) $pad_seq_rand = $pad_seq_rand - $pad_seq_step;

  return $pad_seq_rand;

?>