<?php

  $pad_seq_rand = round ( $pad_seq_rand / $pad_seq_multiple ) * $pad_seq_multiple;

  if ( $pad_seq_rand < $pad_seq_init ) $pad_seq_rand = $pad_seq_rand + $pad_seq_multiple;
  if ( $pad_seq_rand > $pad_seq_exit ) $pad_seq_rand = $pad_seq_rand - $pad_seq_multiple;

  return $pad_seq_rand;

?>