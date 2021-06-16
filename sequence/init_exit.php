<?php

  if ( isset($pad_parms_tag ['from']) or isset($pad_parms_tag ['to']) ) {
    $pad_seq_type = 'from';
    $pad_seq_init = $pad_seq_from;
    $pad_seq_exit = $pad_seq_to;
  } elseif ( isset($pad_parms_tag ['min']) or isset($pad_parms_tag ['max']) ) {
    $pad_seq_type = 'min';
    $pad_seq_init = $pad_seq_min;
    $pad_seq_exit = $pad_seq_max;
  } else {
    $pad_seq_type = '';
    $pad_seq_init = 1; 
    $pad_seq_exit = PHP_INT_MAX;
  }

  if ( $pad_seq_multiple and $pad_seq_init % $pad_seq_multiple )
    $pad_seq_init = ceil ( $pad_seq_init / $pad_seq_multiple ) * $pad_seq_multiple;

  if ( $pad_seq_even and $pad_seq_init % 2 )
    $pad_seq_init++;

  if ( $pad_seq_even and ! $pad_seq_init % 2 )
    $pad_seq_init++;

  if ( $pad_seq_multiple and $pad_seq_exit % $pad_seq_multiple )
    $pad_seq_exit = floor ( $pad_seq_exit / $pad_seq_multiple ) * $pad_seq_multiple;

  if ( $pad_seq_even and $pad_seq_exit % 2 )
    $pad_seq_exit--;

  if ( $pad_seq_even and ! $pad_seq_exit % 2 )
    $pad_seq_exit--;

?>