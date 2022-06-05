<?php

  include_once PAD . "sequence/lib/sequence.php";

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
  $pad_seq_protect  =          $pad_parms_tag ['protect']  ?? 10000; 
  $pad_seq_name     =          $pad_parms_tag ['name']     ?? ''; 

  $pad_seq_tmp = array_key_first($pad_parms_tag) ?? '';

  if ($pad_seq_tmp and ctype_alnum($pad_seq_tmp) and file_exists(PAD . "sequence/types/$pad_seq_tmp") ) {

    $pad_seq_seq = $pad_seq_tmp;

    if ( isset($pad_parms_tag[$pad_seq_seq]) )
      $pad_seq_parm = $pad_parms_tag[$pad_seq_seq];
    elseif ( isset($pad_parms_seq[1]) )
      $pad_seq_parm = $pad_parms_seq[1];
    else
      $pad_seq_parm = '';

  } elseif ( strpos($pad_parm, '..') ) {

      $pad_seq_seq  = 'range';
      $pad_seq_parm = $pad_parm;

  } elseif ( ctype_digit($pad_parm) ) {

      $pad_seq_seq  = 'range';
      $pad_seq_parm = "1..$pad_parm";

  } elseif ( isset($pad_parms_tag['rows']) ) {

      $pad_seq_seq  = 'range';
      $pad_seq_parm = "1.." . $pad_parms_tag['rows'];

  } else {

    $pad_seq_seq  = 'loop';
    $pad_seq_rows = 10;
    $pad_seq_parm = TRUE;

  }

  if ( ! $pad_seq_name )
    $pad_seq_name = $pad_seq_seq; 

  pad_set_arr_var ( 'options_done', $pad_seq_seq, TRUE );
  pad_set_arr_var ( 'options_done', 'rows',       TRUE );
  pad_set_arr_var ( 'options_done', 'page',       TRUE );
  pad_set_arr_var ( 'options_done', 'row',        TRUE );
  pad_set_arr_var ( 'options_done', 'value',      TRUE );
  pad_set_arr_var ( 'options_done', 'min',        TRUE );
  pad_set_arr_var ( 'options_done', 'max',        TRUE );
  pad_set_arr_var ( 'options_done', 'start',      TRUE );
  pad_set_arr_var ( 'options_done', 'end',        TRUE );
  pad_set_arr_var ( 'options_done', 'from',       TRUE );
  pad_set_arr_var ( 'options_done', 'to',         TRUE );
  pad_set_arr_var ( 'options_done', 'unique',     TRUE );
  pad_set_arr_var ( 'options_done', 'random',     TRUE );
  pad_set_arr_var ( 'options_done', 'into',       TRUE );
  pad_set_arr_var ( 'options_done', 'push',       TRUE );
  pad_set_arr_var ( 'options_done', 'pull',       TRUE );
  pad_set_arr_var ( 'options_done', 'protect',    TRUE );

  $pad_seq_init = $pad_seq_base = $pad_seq_result = $pad_seq_prepare = [];
  $pad_seq_cnt  = $pad_seq_protect_cnt = 0;

  for ( $pad_seq_idx = 0; $pad_seq_idx <= 21; $pad_seq_idx++ ) 
    $GLOBALS [ 'pad_seq_sts_' . sprintf('%02d', $pad_seq_idx) ] = 0;

  include 'build/increment.php';
  include 'build/from_to.php';
  include 'build/min_max.php';
  include 'build/start_end.php';
  include 'build/loop_idx_end.php';
  include 'build/init_exit.php';
  include 'build/page.php';
  include 'build/rows.php';

  $pad_seq_max_loops = ($pad_seq_to - $pad_seq_from ) + 1;

  $pad_seq_row   = (!$pad_seq_row)   ? [] : pad_explode ($pad_seq_row,   ';'); 
  $pad_seq_value = (!$pad_seq_value) ? [] : pad_explode ($pad_seq_value, ';');

  if ( $pad_seq_pull )
    if ( $pad_seq_pull === TRUE )
      $pad_seq_pull = array_key_last ($pad_seq_store);

  $pad_seq_build = include 'build/type.php';  

  if ( $pad_seq_build == 'function' ) 
    include_once "functions/$pad_seq_seq.php";

  include "type/$pad_seq_build.php";

  include 'build/actions.php';
  
  if ( $pad_seq_random and !pad_file_exists(PAD . "sequence/types/$pad_seq_seq/random.php") )
    include PAD . 'sequence/actions/list/shuffle.php';  

  if ( $pad_seq_push ) {
    if ( $pad_seq_push === TRUE )
      $pad_seq_push = $pad_seq_name;
    $pad_seq_store[$pad_seq_push] = $pad_seq_result;
    return NULL;
  }

  $pad_data [$pad_lvl] = [];
  foreach ( $pad_seq_result as $pad_v )
     if ( $pad_seq_name == $pad_seq_seq )
      $pad_data [$pad_lvl] [] = [ $pad_seq_name => $pad_v, "sequence" => $pad_v ];    
    else
      $pad_data [$pad_lvl] [] = [ $pad_seq_name => $pad_v ];    

  if ( count ( $pad_data [$pad_lvl] ) )
    return TRUE;
  else
    return FALSE;

?>