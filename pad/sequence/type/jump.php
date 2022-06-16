<?php

  $pad_sequence = $pad_seq_loop_idx;
  
  if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/init.php" )) 
    include PAD . "sequence/types/$pad_seq_seq/init.php";

  if ( isset($pad_seq_prepare [0]) )
    $pad_seq_loop_idx = $pad_seq_prepare [0]; 
  else
    $pad_seq_loop_idx = $pad_sequence;

  while ( TRUE )
    if ( ! include 'go/go.php')
      break;

?>