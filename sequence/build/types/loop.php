<?php

   $n=0;

  if ( $pad_seq_from or $pad_seq_to <> PHP_INT_MAX ) {
    $pad_seq_loop_start = $pad_seq_from;
    $pad_seq_loop_end   = $pad_seq_to;
  }
  elseif ( $pad_seq_min or $pad_seq_max <> PHP_INT_MAX ) {
    $pad_seq_loop_start = $pad_seq_min;
    $pad_seq_loop_end   = $pad_seq_max;
  } else {
    $pad_seq_loop_start = 1;
    $pad_seq_loop_end   = PHP_INT_MAX;
  }

  if ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/order.php" ) ) {
    $pad_seq_loop_start = 1;
    $pad_seq_loop_end   = $pad_seq_to;
    $pad_seq_loop  = 'order';
  }
  elseif ( $pad_seq_loop == 'from_to' 
    or ( ( $pad_seq_from or $pad_seq_to <> PHP_INT_MAX ) and pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/from_to.php") ) ) {
    $pad_seq_loop_start = $pad_seq_from;
    $pad_seq_loop_end   = $pad_seq_to;
    if (!$pad_seq_loop_start)
      $pad_seq_loop_start = 1;
    $pad_seq_loop  = 'from_to';
  }
  elseif ( $pad_seq_loop == 'min_max' 
    or ( ( $pad_seq_min or $pad_seq_max <> PHP_INT_MAX ) and pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/min_max.php") ) ) {
    $pad_seq_loop_start = $pad_seq_min;
    $pad_seq_loop_end   = $pad_seq_max;
    $pad_seq_loop  = 'min_max';
  }
  elseif ( pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/loop.php") ) {
    $pad_seq_loop  = 'loop';
  }
  elseif ( pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/from_to.php") ) {
    if (!$pad_seq_loop_start)
      $pad_seq_loop_start = 1;
    $pad_seq_loop  = 'from_to';
  }
  elseif ( pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/min_max.php") ) {
    $pad_seq_loop  = 'min_max';
  }

  if ( $pad_seq_loop )
    $pad_seq_loop = $pad_seq_loop;

  for ( $pad_seq_loop_idx = $pad_seq_loop_start; $pad_seq_loop_idx <= $pad_seq_loop_end; $pad_seq_loop_idx++ ) {

    $n++;
    $G = $pad_seq_base;

    $pad_sequence = $pad_seq_loop_idx;
    $pad_seq_loop_call = include PAD_HOME . "sequence/types/$pad_tag/$pad_seq_loop.php";    

    if     ( $pad_seq_loop_call === NULL)  $pad_sequence = NULL;
    elseif ( $pad_seq_loop_call === TRUE)  $pad_sequence = $pad_seq_loop_idx;
    elseif ( $pad_seq_loop_call === FALSE) $pad_sequence = FALSE;
    else                                   $pad_sequence = $pad_seq_loop_call;

    if ( ! include 'go/go.php')
      break;

  }

?>