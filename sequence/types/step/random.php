<?php

  $pad_seq_rand = rand (0, $pad_seq_max-$pad_seq_min);

  if     ( $pad_seq_floor ) $pad_seq_steps = floor ( $pad_seq_rand / $pad_seq_step );
  elseif ( $pad_seq_ceil  ) $pad_seq_steps = ceil  ( $pad_seq_rand / $pad_seq_step );
  else                      $pad_seq_steps = round ( $pad_seq_rand / $pad_seq_step );

  $pad_seq_rand = $pad_seq_min + ( $pad_seq_steps * $pad_seq_step );

  if ( $pad_seq_rand > $pad_seq_max ) {
    $pad_seq_steps = floor ( ($pad_seq_max - $pad_seq_min) / $pad_seq_step);
    $pad_seq_rand  = $pad_seq_min + ( $pad_seq_steps * $pad_seq_step );
  }

  return $pad_seq_rand;

?>