<?php

  if ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/init.php" )) 
    $pad_seq_prepare = include PAD_HOME . "sequence/types/$pad_tag/init.php";

  if ( ! is_array($pad_seq_prepare) )
    $pad_seq_prepare = [ 0 => $pad_seq_prepare ];
  
  if ( ! count($pad_seq_prepare) )
    return ;

  $pad_seq_loop_idx = $pad_seq_prepare [0]; 

  while ( TRUE )
    if ( ! include 'go/one.php')
      break;

?>