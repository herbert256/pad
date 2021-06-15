<?php

  $pad_seq_init = $pad_seq_base = $pad_seq_result = [];
  $pad_seq_cnt  = $pad_seq_protect_cnt = 0;

  $pad_seq_loop_idx = 1; 
  $pad_seq_loop_end = PHP_INT_MAX;

  $pad_seq_type = '';

  for ( $pad_seq_idx = 0; $pad_seq_idx <= 18; $pad_seq_idx++ ) 
    $GLOBALS [ 'pad_seq_sts_' . sprintf('%02d', $pad_seq_idx) ] = 0;

  if ( isset($pad_parms_tag ['from']) or isset($pad_parms_tag ['to']) ) 
    $pad_seq_fromto_max =  ( ( intval( $pad_parms_tag ['to'] ?? PHP_INT_MAX ) ) - ( intval ( $pad_parms_tag ['from'] ?? 1 ) ) ) + 1;
  else
    $pad_seq_fromto_max = 0;

  if ( isset($pad_parms_tag ['from']) or isset($pad_parms_tag ['to']) ) {

    $pad_seq_type = 'from';
    $pad_seq_loop_idx = $pad_seq_from;
    $pad_seq_loop_end = $pad_seq_to;

    if ( ! $pad_seq_loop_idx and ! isset($pad_parms_tag ['from']) )
      $pad_seq_loop_idx = 1;    

  } elseif ( isset($pad_parms_tag ['min']) or isset($pad_parms_tag ['max']) ) {

    $pad_seq_type = 'min';
    $pad_seq_loop_idx = $pad_seq_min;
    $pad_seq_loop_end = $pad_seq_max;

    if ( ! $pad_seq_loop_idx and ! isset($pad_parms_tag ['min']) )
      $pad_seq_loop_idx = 1;    

  }

  if ( $pad_seq_page ) {
    $pad_seq_page_start = (($pad_seq_page-1) * $pad_seq_rows) + 1; 
    $pad_seq_page_end   = $pad_seq_page * $pad_seq_rows; 
  }

  if ( $pad_tag == 'pull' ) {
    $pad_pull_start = TRUE;
    $pad_pull_store = ( $pad_parm     ) ? $pad_parm     : 'pad_seq';
    $pad_tag        = ( $pad_seq_into ) ? $pad_seq_into : 'loop';
    $pad_parm       = '';
  }
  else
    $pad_pull_start = FALSE;

  if ( isset($pad_parms_tag [$pad_tag]) )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parms_tag [$pad_tag];
  elseif ( $pad_parm )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parm;
  else
    $GLOBALS ["pad_seq_$pad_tag"] = TRUE;

  $pad_seq_row   = (!$pad_seq_row)   ? [] : pad_explode ($pad_seq_row,   ';'); 
  $pad_seq_value = (!$pad_seq_value) ? [] : pad_explode ($pad_seq_value, ';');

  $pad_seq_dir = 'checks';  include 'dir.php';
  $pad_seq_dir = 'actions'; include 'dir.php';

  $pad_seq_build = include 'type.php';  

  if ( $pad_pull_start ) 
    include PAD_HOME . "sequence/build/pull.php";
  else
    include PAD_HOME . "sequence/build/$pad_seq_build.php";

  include 'options.php';
  
  if ( !$pad_seq_pull and $pad_seq_random and !pad_file_exists(PAD_HOME . "sequence/types/$pad_tag/random.php") )
    include PAD_HOME . 'sequence/options/shuffle.php';  

  if ( $pad_seq_push )
    return include 'push.php';

  return $pad_seq_result; 

?>