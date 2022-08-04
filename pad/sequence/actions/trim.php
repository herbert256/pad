<?php

  if ( ! isset( $pad_prms_tag['both']) and ! isset( $pad_prms_tag['left'])  and !  isset( $pad_prms_tag['right']) )
    $pad_prms_tag['both'] = TRUE;

  if ( isset( $pad_prms_tag['both']) ) {
    $pad_seq_trucate_count = pad_seq_get_count ( 'both', 'truncate', 'count');
    $pad_seq_result        = pad_seq_truncate  ( $pad_seq_result, 'left',  $pad_seq_trucate_count );
    $pad_seq_result        = pad_seq_truncate  ( $pad_seq_result, 'right', $pad_seq_trucate_count );
    pad_set_arr_var ( 'done', 'both', TRUE );
  }

  if ( isset( $pad_prms_tag['left']) ) {
    $pad_seq_trucate_count = pad_seq_get_count ( 'left', 'truncate', 'count');
    $pad_seq_result        = pad_seq_truncate  ( $pad_seq_result, 'left', $pad_seq_trucate_count );
    pad_set_arr_var ( 'done', 'left', TRUE );
  }

  if ( isset( $pad_prms_tag['right']) ) {
    $pad_seq_trucate_count = pad_seq_get_count ( 'right', 'truncate', 'count');
    $pad_seq_result        = pad_seq_truncate  ( $pad_seq_result, 'right', $pad_seq_trucate_count );
    pad_set_arr_var ( 'done', 'right', TRUE );
  }

  return $pad_seq_result;
  
?>