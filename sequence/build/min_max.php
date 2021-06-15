<?php

  $pad_seq_type = 'min';

  $pad_seq_loop_idx = $pad_seq_min;
  $pad_seq_loop_end = $pad_seq_max;

  if ( ! $pad_seq_loop_idx and ! isset($pad_parms_tag ['min']) )
    $pad_seq_loop_idx = 1;  

  include PAD_HOME . "sequence/build/go/loop.php"; 

?>