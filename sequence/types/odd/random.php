<?php

  if ( ! ($pad_seq_rand % 2) )
    $pad_seq_rand++;

  if ( $pad_seq_rand < $pad_seq_init ) $pad_seq_rand = $pad_seq_rand + 2;
  if ( $pad_seq_rand > $pad_seq_exit ) $pad_seq_rand = $pad_seq_rand - 2;

  return $pad_seq_rand;

?>