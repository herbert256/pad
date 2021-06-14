<?php

  $pad_seq_type = 'min';

  $pad_seq_loop_start = $pad_seq_min;
  $pad_seq_loop_end   = $pad_seq_max;

  if ( ! $pad_seq_loop_start and ! isset($pad_parms_tag ['min']) )
    $pad_seq_loop_start = 1;  

  include PAD_HOME . "sequence/build/go/loop.php"; 

?>