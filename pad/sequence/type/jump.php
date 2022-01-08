<?php

  $pad_sequence = $pad_seq_loop_idx;
  
  if ( pad_file_exists ( PAD . "sequence/types/$pad_tag/init.php" )) 
    $pad_seq_init_return = include PAD . "sequence/types/$pad_tag/init.php";

  if ( isset($pad_seq_prepare [0]) )
    $pad_seq_loop_idx = $pad_seq_prepare [0]; 
  else
    $pad_seq_loop_idx = $pad_sequence;

  while ( TRUE )
    if ( ! include 'go/one.php')
      break;

?>