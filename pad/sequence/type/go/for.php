<?php

  foreach ( $pad_seq_for as $pad_seq_loop_idx ) {

    if ( $pad_seq_random )
      $pad_seq_loop_idx  = $pad_seq_for [array_rand($pad_seq_for)] ;

    include 'go.php';

  }

?>