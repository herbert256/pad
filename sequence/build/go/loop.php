<?php

  if ( isset($pad_parms_tag ['desending']) ) 
    for ( $pad_seq_loop_idx = $pad_seq_loop_end; $pad_seq_loop_idx >= $pad_seq_loop_start; $pad_seq_loop_idx-- ) {
      if ( ! include 'loop_go.php')
        break;
    }
  else
    for ( $pad_seq_loop_idx = $pad_seq_loop_start; $pad_seq_loop_idx <= $pad_seq_loop_end; $pad_seq_loop_idx++ ) {
      if ( ! include 'loop_go.php')
        break;
    }

?>