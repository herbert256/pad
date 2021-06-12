<?php

  $pad_seq_init_cnt = 0;

  if ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/init.php" )) 
    $pad_seq_init = include PAD_HOME . "sequence/types/$pad_tag/init.php";
  else
    $pad_seq_init = $pad_seq_from;

if ( ! is_array($pad_seq_init) )
    $pad_seq_init = [ 0 => $pad_seq_init ];
  
  if ( ! count($pad_seq_init) )
    return ;
    
  $pad_sequence = $pad_seq_init [0]; 

  while ( TRUE ) {

    if ( ! include 'one.php')
      break;

    $pad_seq_init_cnt++;

    if ( isset ( $pad_seq_init [$pad_seq_init_cnt] ) )

      $pad_sequence = $pad_seq_init [$pad_seq_init_cnt];
   
    elseif ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/jump.php" ) ) {

      $G = &$pad_seq_base; 
      $n = count($pad_seq_base) - 1;

      $pad_sequence = include PAD_HOME . "sequence/types/$pad_tag/jump.php"; 

    } else 

      break;

  }

?>