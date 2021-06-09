<?php

  $pad_seq_init = include 'first.php';
 
  if ( ! is_array ($pad_seq_init) )
    return;

  $pad_seq_now = $pad_seq_init [0]; 

  while ( 1 ) {

    if ( ! include 'one.php')
      break;

    $pad_seq_now = include 'next.php';

    if ( is_null($pad_seq_now) or $pad_seq_now === FALSE )
      break;
  
  }

?>