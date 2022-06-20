<?php
 
  if ( $pad_seq_pull             ) return; 
  if ( $pad_seq_seq == 'range'   ) return;
  if ( $pad_seq_rows             ) return;
  if ( $pad_seq_to < PHP_INT_MAX ) return;

  $pad_seq_rows = $pad_seq_save;

?>