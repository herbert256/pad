<?php

  include_once PAD . "sequence/lib/sequence.php";

  $pad_seq_rows    = intval ( $pad_parms_tag ['rows']      ?? 0           );
  $pad_seq_first   = intval ( $pad_parms_tag ['first']     ?? 0           );
  $pad_seq_last    = intval ( $pad_parms_tag ['last']      ?? PHP_INT_MAX );
  $pad_seq_min     = intval ( $pad_parms_tag ['min']       ?? 0           );
  $pad_seq_max     = intval ( $pad_parms_tag ['max']       ?? PHP_INT_MAX );
  $pad_seq_from    = intval ( $pad_parms_tag ['from']      ?? 1           );
  $pad_seq_inc     = intval ( $pad_parms_tag ['increment'] ?? 1           );
  $pad_seq_to      = intval ( $pad_parms_tag ['to']        ?? PHP_INT_MAX );
  $pad_seq_unique  = intval ( $pad_parms_tag ['unique']    ?? 0           );
  $pad_seq_random  = intval ( $pad_parms_tag ['random']    ?? 0           );
  $pad_seq_into    =          $pad_parms_tag ['into']      ?? '';
  $pad_seq_push    =          $pad_parms_tag ['push']      ?? '';
  $pad_seq_pull    =          $pad_parms_tag ['pull']      ?? ''; 
  $pad_seq_name    =          $pad_parms_tag ['name']      ?? ''; 
  $pad_seq_data    =          $pad_parms_tag ['toData']    ?? ''; 
  $pad_seq_protect =          $pad_parms_tag ['protect']   ?? 1000; 
  $pad_seq_save    =          $pad_parms_tag ['save']      ?? 100; 

  include 'build/sequence.php';
  include 'build/name.php';
  include 'build/bool.php';
  include 'build/loop.php';

  pad_set_arr_var ( 'options_done', $pad_seq_seq,  TRUE );
  pad_set_arr_var ( 'options_done', $pad_seq_name, TRUE );
  pad_set_arr_var ( 'options_done', 'rows',        TRUE );
  pad_set_arr_var ( 'options_done', 'min',         TRUE );
  pad_set_arr_var ( 'options_done', 'max',         TRUE );
  pad_set_arr_var ( 'options_done', 'from',        TRUE );
  pad_set_arr_var ( 'options_done', 'to',          TRUE );
  pad_set_arr_var ( 'options_done', 'unique',      TRUE );
  pad_set_arr_var ( 'options_done', 'random',      TRUE );
  pad_set_arr_var ( 'options_done', 'into',        TRUE );
  pad_set_arr_var ( 'options_done', 'push',        TRUE );
  pad_set_arr_var ( 'options_done', 'pull',        TRUE );
  pad_set_arr_var ( 'options_done', 'protect',     TRUE );
  pad_set_arr_var ( 'options_done', 'random',      TRUE );

  $pad_seq_result = $pad_seq_result = $pad_seq_for = [];
  $pad_seq_protect_cnt = 0;

  if ( $pad_seq_pull === TRUE ) $pad_seq_pull = array_key_last ($pad_seq_store);
  if ( $pad_seq_push === TRUE ) $pad_seq_push = $pad_seq_name;

  $pad_seq_build = include 'build/type.php';  

  if ( $pad_seq_seq == 'random' or $pad_seq_random )  
    $pad_seq_random = TRUE;

  if ( $pad_seq_build == 'function' ) include_once "types/$pad_seq_seq/function.php";
  if ( $pad_seq_build == 'bool'     ) include_once "types/$pad_seq_seq/bool.php";

  if ( pad_file_exists ( PAD . "sequence/types/$pad_seq_seq/init.php" )) 
    include PAD . "sequence/types/$pad_seq_seq/init.php";
      
  if ( $pad_seq_pull )
    include "build/pull.php";

  if ( $pad_seq_pull or count($pad_seq_for) ) 
    include "type/for.php";
  else {
    include 'build/save.php';
    include "type/$pad_seq_build.php";
  }

  include 'build/actions.php';
 
  if ( $pad_seq_push ) {
    $pad_seq_store[$pad_seq_push] = $pad_seq_result;
    return NULL;
  }

  return $pad_seq_result;

?>