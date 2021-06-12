<?php

  $pad_seq_rand = rand ( $pad_seq_from, $pad_seq_to);

  if     ( $pad_seq_floor ) $pad_seq_rand = floor ( $pad_seq_rand / $pad_seq_multiple ) * $pad_seq_multiple;
  elseif ( $pad_seq_ceil  ) $pad_seq_rand = ceil  ( $pad_seq_rand / $pad_seq_multiple ) * $pad_seq_multiple;
  else                      $pad_seq_rand = round ( $pad_seq_rand / $pad_seq_multiple ) * $pad_seq_multiple;

  if ( $pad_seq_rand < $pad_seq_from ) $pad_seq_rand = $pad_seq_rand + $pad_seq_multiple;
  if ( $pad_seq_rand > $pad_seq_to ) $pad_seq_rand = $pad_seq_rand - $pad_seq_multiple;

  return $pad_seq_rand;

?>