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

  $pad_seq_init = pad_seq_check_even_odd_up   ( $pad_seq_init, $pad_seq_even, $pad_seq_odd );
  $pad_seq_init = pad_seq_check_multiple_up   ( $pad_seq_init, $pad_seq_multiple );

  $pad_seq_exit = pad_seq_check_even_odd_down ( $pad_seq_exit, $pad_seq_even, $pad_seq_odd );
  $pad_seq_exit = pad_seq_check_multiple_down ( $pad_seq_exit, $pad_seq_multiple );
  
?>