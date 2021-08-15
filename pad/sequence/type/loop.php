<?php

  $pad_seq_go = $pad_seq_loop_idx;

  while ( $pad_seq_go <= $pad_seq_loop_end ) {

    $pad_seq_loop_idx = $pad_seq_go;

    if ( ! include 'go/one.php')
        break;

    $pad_seq_go = $pad_seq_go + $pad_seq_increment;

  }

?>