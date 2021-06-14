<?php

  while ( TRUE ) {

    $pad_seq_rand = rand ( $pad_seq_init, $pad_seq_exit );

    $pad_sequence = include PAD_HOME . "sequence/types/$pad_tag/random.php";

    if ( $pad_sequence < $pad_seq_init ) $pad_sequence = false;
    if ( $pad_sequence > $pad_seq_exit ) $pad_sequence = false;

    if ( ! include 'go/go.php')
      break;

  }

?>