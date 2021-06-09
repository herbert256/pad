<?php

  $pad_seq_init_cnt = 0;

  $pad_seq_init = include 'first.php';
 
  if ( ! is_array ($pad_seq_init) )
    return;

  $pad_sequence = $pad_seq_init [0]; 

  while ( 1 ) {

    if ( is_null($pad_sequence) or $pad_sequence === FALSE )
      break;
  
    if ( ! include 'one.php')
      break;

    $pad_sequence = include 'next.php';

  }

?>