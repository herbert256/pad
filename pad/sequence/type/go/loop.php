<?php

  $pad_seq_go = $pad_sequence = $pad_seq_loop_idx = $pad_loop_idx_start;

  while ( $pad_seq_go <= $pad_seq_loop_end ) {

    if ( $pad_seq_random )
      $pad_seq_loop_idx = pad_seq_random ( $pad_seq_random_start, $pad_seq_random_end );
    else
      $pad_seq_loop_idx = $pad_seq_go;

    if ( ! include 'go.php')
        break;

    $pad_seq_go = $pad_seq_go + $pad_seq_loop_increment;

  }

?>