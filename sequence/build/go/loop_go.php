<?php

  if ( $pad_seq_random )
    $pad_seq_rand = rand ( $pad_seq_init, $pad_seq_exit );

  $n++;
  $G = $pad_seq_base;

  $pad_sequence = $pad_seq_loop_idx;
  $pad_seq_loop_call = include PAD_HOME . "sequence/types/$pad_tag/$pad_seq_build.php";    

  if     ( $pad_seq_loop_call === NULL)  $pad_sequence = NULL;
  elseif ( $pad_seq_loop_call === TRUE)  $pad_sequence = $pad_seq_loop_idx;
  elseif ( $pad_seq_loop_call === FALSE) $pad_sequence = FALSE;
  else                                   $pad_sequence = $pad_seq_loop_call;

  if ( ! include 'go.php')
    return false;

  return true;

?>