<?php

  include_once PAD . "sequence/lib/sequence.php";

  $pad_seq_from     = intval ( $pad_parms_tag ['from']      ?? 1           );
  $pad_seq_inc      = intval ( $pad_parms_tag ['increment'] ?? 1           );
  $pad_seq_to       = intval ( $pad_parms_tag ['to']        ?? PHP_INT_MAX );
  $pad_seq_rows     = intval ( $pad_parms_tag ['rows']      ?? 0           );
  $pad_seq_min      = intval ( $pad_parms_tag ['min']       ?? 0           );
  $pad_seq_max      = intval ( $pad_parms_tag ['max']       ?? PHP_INT_MAX );
  $pad_seq_start    = intval ( $pad_parms_tag ['start']     ?? 0           );
  $pad_seq_end      = intval ( $pad_parms_tag ['end']       ?? PHP_INT_MAX );
  $pad_seq_unique   = intval ( $pad_parms_tag ['unique']    ?? 0           );
  $pad_seq_random   = intval ( $pad_parms_tag ['random']    ?? 0           );
  $pad_seq_count    = intval ( $pad_parms_tag ['count']     ?? 0           );
  $pad_seq_page     = intval ( $pad_parms_tag ['page']      ?? 0           );
  $pad_seq_name     =          $pad_parms_tag ['name']      ?? ''; 
  $pad_seq_protect  =          $pad_parms_tag ['protect']   ?? 1000; 
  $pad_seq_save     =          $pad_parms_tag ['save']      ?? 100; 
  $pad_seq_unique   =          $pad_parms_tag ['unique']    ?? '';
  $pad_seq_push     =          $pad_parms_tag ['store']     ?? ''; 
  $pad_seq_pull     =          $pad_parms_tag ['sequence']  ?? '';
  $pad_seq_range    =          $pad_parms_tag ['range']     ?? '';
  $pad_seq_filter   =          $pad_parms_tag ['filter']    ?? '';
  $pad_seq_make     =          $pad_parms_tag ['make']      ?? '';

  $pad_seq_parm = '';
  $pad_seq_result = $pad_seq_for = $pad_seq_make_list = $pad_seq_cache = $pad_seq_filter_list = [];
  $pad_seq = $pad_sequence = $pad_seq_protect_cnt = $pad_seq_base = 0;

  unset ( $pad_parms_tag ['store'] );
  unset ( $pad_parameters [$pad_lvl] ['parms_tag'] ['store'] );

  include 'build/sequence.php';

  if   ( $pad_seq_name ) $pad_seq_name = $pad_seq_name;
  else                   $pad_seq_name = $pad_seq_set; 

  include 'build/loop.php';
  include 'build/make.php';
  include 'build/filter.php';
  include 'build/operations.php';
  include 'build/page.php';

  if ( ! isset($GLOBALS ["pad_seq_$pad_seq_seq"]) ) $GLOBALS ["pad_seq_$pad_seq_seq"] = $pad_seq_parm;
  if ( ! isset($pad_parms_tag ["$pad_seq_seq"])   ) $pad_parms_tag ["$pad_seq_seq"]   = $pad_seq_parm;

  foreach ( $pad_parms_tag as $pad_seq_tag_name => $pad_seq_tag_value ) {
    if ( ! isset($GLOBALS ["pad_seq_$pad_seq_tag_name"]) )
      $GLOBALS ["pad_seq_$pad_seq_tag_name"] = $pad_seq_tag_value;
    if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_tag_name/make.php" ) )
      pad_set_arr_var ( 'options_done', $pad_seq_tag_name, TRUE );
    if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_tag_name/filter.php" ) )
      pad_set_arr_var ( 'options_done', $pad_seq_tag_name, TRUE );
  }

  pad_set_arr_var ( 'options_done', $pad_seq_seq,  TRUE );
  pad_set_arr_var ( 'options_done', $pad_seq_name, TRUE );
  pad_set_arr_var ( 'options_done', 'from',        TRUE );
  pad_set_arr_var ( 'options_done', 'increment',   TRUE );
  pad_set_arr_var ( 'options_done', 'to',          TRUE );
  pad_set_arr_var ( 'options_done', 'rows',        TRUE );
  pad_set_arr_var ( 'options_done', 'min',         TRUE );
  pad_set_arr_var ( 'options_done', 'max',         TRUE );
  pad_set_arr_var ( 'options_done', 'unique',      TRUE );
  pad_set_arr_var ( 'options_done', 'page',        TRUE );
  pad_set_arr_var ( 'options_done', 'start',       TRUE );
  pad_set_arr_var ( 'options_done', 'end',         TRUE );
  pad_set_arr_var ( 'options_done', 'random',      TRUE );
  pad_set_arr_var ( 'options_done', 'push',        TRUE );
  pad_set_arr_var ( 'options_done', 'store',       TRUE );
  pad_set_arr_var ( 'options_done', 'pull',        TRUE );
  pad_set_arr_var ( 'options_done', 'sequence',    TRUE );
  pad_set_arr_var ( 'options_done', 'range',       TRUE );
  pad_set_arr_var ( 'options_done', 'protect',     TRUE );
  pad_set_arr_var ( 'options_done', 'filter',      TRUE );
  pad_set_arr_var ( 'options_done', 'make',        TRUE );

  $pad_seq_build = include 'build/type.php';  

  if ( $pad_seq_build == 'function' ) include_once "types/$pad_seq_seq/function.php";
  if ( $pad_seq_build == 'bool'     ) include_once "types/$pad_seq_seq/bool.php";

  if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/init.php" )) 
    include PAD . "sequence/types/$pad_seq_seq/init.php";
      
  include 'type/type.php';

  include 'build/actions.php';
 
  if ( $pad_seq_push )
    return include 'build/push.php';

  if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/exits.php" )) 
    return include PAD . "sequence/types/$pad_seq_seq/exits.php";

  $pad_name = $pad_parameters [$pad_lvl] ['name'] = $pad_seq_name;

  return $pad_seq_result;

?>