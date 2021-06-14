<?php

  $pad_seq_fixed = include PAD_HOME . "sequence/types/$pad_tag/fixed.php" ;
  
  foreach ( $pad_seq_fixed as $pad_sequence ) {

    if ( ! include 'go/go.php')
      break;

  }

?>