<?php

  if ( isset($pad_parms_tag ['from']) or isset($pad_parms_tag ['to']) ) 
    $pad_seq_fromto_max =  ( ( intval( $pad_parms_tag ['to'] ?? PHP_INT_MAX ) ) - ( intval ( $pad_parms_tag ['from'] ?? 1 ) ) ) + 1;
  else
    $pad_seq_fromto_max = 0;

  if ( isset($pad_parms_tag ['from']) or isset($pad_parms_tag ['to']) ) {
    $pad_seq_loop_idx = $pad_seq_from;
    $pad_seq_loop_end = $pad_seq_to;
  } else {
    $pad_seq_loop_idx = 1; 
    $pad_seq_loop_end = PHP_INT_MAX;
  }

  $pad_seq_loop_idx = pad_seq_check ( $pad_seq_loop_idx, $pad_seq_increment, $pad_seq_even, $pad_seq_odd );
  $pad_seq_loop_end = pad_seq_check ( $pad_seq_loop_end, $pad_seq_increment, $pad_seq_even, $pad_seq_odd );

?>