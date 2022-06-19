<?php
  
  if ( $pad_seq_seq == 'range'   ) return;
  if ( $pad_seq_rows             ) return;
  if ( $pad_seq_to < PHP_INT_MAX ) return;

  $pad_seq_end = $pad_seq_start + $pad_seq_save;
  $pad_seq_max = $pad_seq_save;

?>