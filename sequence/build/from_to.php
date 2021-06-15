<?php

  $pad_seq_type = 'from';

  $pad_seq_loop_idx = $pad_seq_from;
  $pad_seq_loop_end = $pad_seq_to;

  if ( ! $pad_seq_loop_idx and ! isset($pad_parms_tag ['from']) )
    $pad_seq_loop_idx = 1;

  include "go/loop.php"; 

?>