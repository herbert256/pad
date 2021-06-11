<?php

  $pad_seq_init_cnt = 0;

  if ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/init.php" )) 
    $pad_seq_init = include PAD_HOME . "sequence/types/$pad_tag/init.php";
  else
    $pad_seq_init = $pad_seq_min;

  if ( is_null($pad_seq_init) or $pad_seq_init === FALSE or ( is_array($pad_seq_init) and ! count($pad_seq_init) ) )
    return ;
     
  if ( ! is_array($pad_seq_init) ) {
    $pad_seq_save = $pad_seq_init;
    $pad_seq_init = [];
    $pad_seq_init [] = $pad_seq_save;
  }
 
  $pad_sequence = $pad_seq_init [0]; 


  while ( TRUE ) {

    if ( is_null($pad_sequence) or $pad_sequence === FALSE )
      break;

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

      $pad_sequence = FALSE;
  }

?>