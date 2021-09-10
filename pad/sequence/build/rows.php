<?php

  if ( $pad_seq_value or $pad_seq_rows or $pad_seq_row )
    return;

  if ( $pad_seq_from <> 1 or $pad_seq_to  <> PHP_INT_MAX ) return;
  if ( $pad_seq_start     or $pad_seq_end <> PHP_INT_MAX ) return;
  if ( $pad_seq_min       or $pad_seq_max <> PHP_INT_MAX ) return;

  $pad_seq_rows = 100;

?>