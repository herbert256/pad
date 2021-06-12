<?php

  $pad_seq_rand  = rand (0, $pad_seq_to-$pad_seq_from);
  $pad_seq_steps = round ( $pad_seq_rand / $pad_seq_step );
  $pad_seq_rand  = $pad_seq_from + ( $pad_seq_steps * $pad_seq_step );

  return $pad_seq_rand;

?>