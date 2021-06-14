<?php

  $pad_seq_rand  = $pad_seq_exit - $pad_seq_rand;
  $pad_seq_steps = round ( $pad_seq_rand / $pad_seq_step );
  $pad_seq_rand  = $pad_seq_init + ( $pad_seq_steps * $pad_seq_step );

  if ( $pad_seq_rand < $pad_seq_init ) $pad_seq_rand = $pad_seq_rand + $pad_seq_step;
  if ( $pad_seq_rand > $pad_seq_exit ) $pad_seq_rand = $pad_seq_rand - $pad_seq_step;

  return $pad_seq_rand;

?>