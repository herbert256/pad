<?php

  $n=0;

  if ( isset($pad_parms_tag ['desending']) ) 

    for ( $pad_seq_loop_idx = $pad_seq_loop_end; $pad_seq_loop_idx >= $pad_seq_loop_start; $pad_seq_loop_idx-- ) {

      $n++;
      $G = $pad_seq_base;

      $pad_sequence = $pad_seq_loop_idx;
      $pad_seq_loop_call = include PAD_HOME . "sequence/types/$pad_tag/$pad_seq_build.php";    

      if     ( $pad_seq_loop_call === NULL)  $pad_sequence = NULL;
      elseif ( $pad_seq_loop_call === TRUE)  $pad_sequence = $pad_seq_loop_idx;
      elseif ( $pad_seq_loop_call === FALSE) $pad_sequence = FALSE;
      else                                   $pad_sequence = $pad_seq_loop_call;

      if ( ! include 'go.php')
        break;

    }

  else

    for ( $pad_seq_loop_idx = $pad_seq_loop_start; $pad_seq_loop_idx <= $pad_seq_loop_end; $pad_seq_loop_idx++ ) {

      $n++;
      $G = $pad_seq_base;

      $pad_sequence = $pad_seq_loop_idx;
      $pad_seq_loop_call = include PAD_HOME . "sequence/types/$pad_tag/$pad_seq_build.php";    

      if     ( $pad_seq_loop_call === NULL)  $pad_sequence = NULL;
      elseif ( $pad_seq_loop_call === TRUE)  $pad_sequence = $pad_seq_loop_idx;
      elseif ( $pad_seq_loop_call === FALSE) $pad_sequence = FALSE;
      else                                   $pad_sequence = $pad_seq_loop_call;

      if ( ! include 'go.php')
        break;

    }

?>