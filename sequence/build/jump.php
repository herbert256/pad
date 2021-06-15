<?php

  if ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/init.php" )) 
    $pad_seq_init = include PAD_HOME . "sequence/types/$pad_tag/init.php";

  if ( ! is_array($pad_seq_init) )
    $pad_seq_init = [ 0 => $pad_seq_init ];
  
  if ( ! count($pad_seq_init) )
    return ;

  $pad_seq_init = $pad_seq_init [0]; 

  include "go/loop.php"; 

?>