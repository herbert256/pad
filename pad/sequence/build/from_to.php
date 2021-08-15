<?php

  $pad_seq_fromto = ( isset($pad_parms_tag ['from']) or isset($pad_parms_tag ['to']) );

  if ( isset($pad_parms_tag ['from']) or isset($pad_parms_tag ['to']) ) 
    $pad_seq_fromto_max =  ( ( intval( $pad_parms_tag ['to'] ?? PHP_INT_MAX ) ) - ( intval ( $pad_parms_tag ['from'] ?? 1 ) ) ) + 1;
  else
    $pad_seq_fromto_max = 0;

  $pad_seq_from = pad_seq_check_even_odd_up   ( $pad_seq_from, $pad_seq_even, $pad_seq_odd );
  $pad_seq_from = pad_seq_check_multiple_up   ( $pad_seq_from, $pad_seq_multiple );

  $pad_seq_to   = pad_seq_check_even_odd_down ( $pad_seq_to,   $pad_seq_even, $pad_seq_odd );
  $pad_seq_to   = pad_seq_check_multiple_down ( $pad_seq_to,   $pad_seq_multiple );

?>