<?php

  for ( $pad_sequence = $pad_seq_min; $pad_sequence <= $pad_seq_max; $pad_sequence++ ) {

    $pad_sequence = include PAD_HOME . "sequence/types/$pad_tag/loop.php";

    if ( ! include 'one.php')
      break;

  }

?>