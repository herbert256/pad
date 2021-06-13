<?php

  $pad_seq_rand = rand  ( $pad_seq_min, $pad_seq_max);
  $pad_seq_rand = round ( $pad_seq_rand / $pad_seq_multiple ) * $pad_seq_multiple;

  if ( $pad_seq_rand < $pad_seq_min ) $pad_seq_rand = $pad_seq_rand + $pad_seq_multiple;
  if ( $pad_seq_rand > $pad_seq_max ) $pad_seq_rand = $pad_seq_rand - $pad_seq_multiple;

  return $pad_seq_rand;

?>