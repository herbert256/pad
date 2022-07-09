<?php

  if ( $pad_seq_seq == 'range'   ) return;
  if ( $pad_seq_rows             ) return;
  if ( $pad_seq_to < PHP_INT_MAX ) return;

  if ( $pad_seq_random and $pad_seq_max )
    $pad_seq_to = $pad_seq_loop_end = $pad_seq_max;

  $pad_seq_rows = $pad_seq_save;

?>