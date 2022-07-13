<?php

  if ( ! isset( $pad_parms_tag['both']) and ! isset( $pad_parms_tag['left'])  and !  isset( $pad_parms_tag['right']) )
    $pad_parms_tag['both'] = TRUE;

  if ( isset( $pad_parms_tag['both']) ) {
    $pad_seq_trucate_count = pad_seq_get_count ( 'both', 'truncate', 'count');
    $pad_seq_result        = pad_seq_truncate  ( $pad_seq_result, 'left',  $pad_seq_trucate_count );
    $pad_seq_result        = pad_seq_truncate  ( $pad_seq_result, 'right', $pad_seq_trucate_count );
    pad_set_arr_var ( 'options_done', 'both', TRUE );
  }

  if ( isset( $pad_parms_tag['left']) ) {
    $pad_seq_trucate_count = pad_seq_get_count ( 'left', 'truncate', 'count');
    $pad_seq_result        = pad_seq_truncate  ( $pad_seq_result, 'left', $pad_seq_trucate_count );
    pad_set_arr_var ( 'options_done', 'left', TRUE );
  }

  if ( isset( $pad_parms_tag['right']) ) {
    $pad_seq_trucate_count = pad_seq_get_count ( 'right', 'truncate', 'count');
    $pad_seq_result        = pad_seq_truncate  ( $pad_seq_result, 'right', $pad_seq_trucate_count );
    pad_set_arr_var ( 'options_done', 'right', TRUE );
  }

  return $pad_seq_result;
  
?>