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

  if ( $pad_seq_multiple and $pad_seq_loop_idx % $pad_seq_multiple )
    $pad_seq_loop_idx = ceil ( $pad_seq_loop_idx / $pad_seq_multiple ) * $pad_seq_multiple;

  if ( $pad_seq_even and $pad_seq_loop_idx % 2 )
    $pad_seq_loop_idx++;

  if ( $pad_seq_even and ! $pad_seq_loop_idx % 2 )
    $pad_seq_loop_idx++;

  if ( $pad_seq_multiple and $pad_seq_loop_end % $pad_seq_multiple )
    $pad_seq_loop_end = floor ( $pad_seq_loop_end / $pad_seq_multiple ) * $pad_seq_multiple;

  if ( $pad_seq_even and $pad_seq_loop_end % 2 )
    $pad_seq_loop_end--;

  if ( $pad_seq_even and ! $pad_seq_loop_end % 2 )
    $pad_seq_loop_end--;

?>