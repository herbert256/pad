<?php

  $pad_seq_loop_start = $pad_seq_from;
  $pad_seq_loop_end   = $pad_seq_to;

  if ( ! $pad_seq_loop_start and ! isset($pad_parms_tag ['from']) )
    $pad_seq_loop_start = 1;

  $pad_seq_cnt = intval($pad_seq_loop_start) - 1;

  include PAD_HOME . "sequence/build/go/loop.php"; 

?>