<?php

  include_once PAD_HOME . "pad/sequence/lib/sequence.php";

  $pad_seq_rows     = intval ( $pad_parms_tag ['rows']     ?? 0           );
  $pad_seq_page     = intval ( $pad_parms_tag ['page']     ?? 0           );
  $pad_seq_row      =          $pad_parms_tag ['row']      ?? 0;
  $pad_seq_value    =          $pad_parms_tag ['value']    ?? 0;
  $pad_seq_first    = intval ( $pad_parms_tag ['first']    ?? 0           );
  $pad_seq_last     = intval ( $pad_parms_tag ['last']     ?? PHP_INT_MAX );
  $pad_seq_min      = intval ( $pad_parms_tag ['min']      ?? 0           );
  $pad_seq_max      = intval ( $pad_parms_tag ['max']      ?? PHP_INT_MAX );
  $pad_seq_from     = intval ( $pad_parms_tag ['from']     ?? 1           );
  $pad_seq_to       = intval ( $pad_parms_tag ['to']       ?? PHP_INT_MAX );
  $pad_seq_start    = intval ( $pad_parms_tag ['start']    ?? 0           );
  $pad_seq_end      = intval ( $pad_parms_tag ['end']      ?? PHP_INT_MAX );
  $pad_seq_unique   = intval ( $pad_parms_tag ['unique']   ?? 0           );
  $pad_seq_random   = intval ( $pad_parms_tag ['random']   ?? 0           );
  $pad_seq_into     =          $pad_parms_tag ['into']     ?? '';
  $pad_seq_push     =          $pad_parms_tag ['push']     ?? '';
  $pad_seq_pull     =          $pad_parms_tag ['pull']     ?? ''; 
  $pad_seq_checks   =          $pad_parms_tag ['checks']   ?? [];  
  $pad_seq_actions  =          $pad_parms_tag ['actions']  ?? [];  
  $pad_seq_protect  =          $pad_parms_tag ['protect']  ?? 10000000;  

  pad_set_arr_var ( 'options_done', 'rows',     TRUE );
  pad_set_arr_var ( 'options_done', 'page',     TRUE );
  pad_set_arr_var ( 'options_done', 'row',      TRUE );
  pad_set_arr_var ( 'options_done', 'value',    TRUE );
  pad_set_arr_var ( 'options_done', 'min',      TRUE );
  pad_set_arr_var ( 'options_done', 'max',      TRUE );
  pad_set_arr_var ( 'options_done', 'start',    TRUE );
  pad_set_arr_var ( 'options_done', 'end',      TRUE );
  pad_set_arr_var ( 'options_done', 'from',     TRUE );
  pad_set_arr_var ( 'options_done', 'to',       TRUE );
  pad_set_arr_var ( 'options_done', 'unique',   TRUE );
  pad_set_arr_var ( 'options_done', 'random',   TRUE );
  pad_set_arr_var ( 'options_done', 'into',     TRUE );
  pad_set_arr_var ( 'options_done', 'push',     TRUE );
  pad_set_arr_var ( 'options_done', 'pull',     TRUE );
  pad_set_arr_var ( 'options_done', 'protect',  TRUE );
  pad_set_arr_var ( 'options_done', 'checks',   TRUE );
  pad_set_arr_var ( 'options_done', 'actions',  TRUE );

  if ( isset($pad_parms_tag ['walk']) )
    return include 'walk.php';
  else 
    return include 'build.php';

?>