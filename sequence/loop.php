<?php

  for ( $pad_seq_check = $pad_seq_min; $pad_seq_check <= $pad_seq_max; $pad_seq_check++ ) {

    $pad_seq_now = include PAD_HOME . "sequence/types/$pad_tag/loop.php";

    if ( ! include 'one.php')
      break;

  }

?>