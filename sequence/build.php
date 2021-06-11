<?php

  $pad_seq_rows     = $pad_parms_tag ['rows']   ?? 0;
  $pad_seq_row      = $pad_parms_tag ['row']    ?? 0;
  $pad_seq_value    = $pad_parms_tag ['value']  ?? 0;
  $pad_seq_min      = $pad_parms_tag ['from']   ?? 1;
  $pad_seq_max      = $pad_parms_tag ['to']     ?? 0;
  $pad_seq_start    = $pad_parms_tag ['start']  ?? 0;
  $pad_seq_floor    = $pad_parms_tag ['floor']  ?? 0;
  $pad_seq_ceil     = $pad_parms_tag ['ceil']   ?? 0;
  $pad_seq_end      = $pad_parms_tag ['end']    ?? 0;
  $pad_seq_unique   = $pad_parms_tag ['unique'] ?? 0;
  $pad_seq_random   = $pad_parms_tag ['random'] ?? 0;
  $pad_seq_skip     = $pad_parms_tag ['skip']   ?? 10000;
  $pad_seq_push     = $pad_parms_tag ['push']   ?? '';
  $pad_seq_pull     = $pad_parms_tag ['pull']   ?? '';
  $pad_seq_build    = $pad_parms_tag ['build']  ?? '';

  $pad_seq_skip_cnt = 0;

  if ( $pad_seq_unique ) {
    $pad_seq_unique_cnt = 0;
    if ( $pad_seq_unique === TRUE )
      $pad_seq_unique = 250;
  }

  if ( ! $pad_seq_max )
    $pad_seq_max = PHP_INT_MAX;

  if ( isset($pad_parms_tag [$pad_tag]) )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parms_tag [$pad_tag];
  elseif ( $pad_parm )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parm;
  else
    $GLOBALS ["pad_seq_$pad_tag"] = 1;

  $pad_seq_multi_rows = [];
  if ( strpos($pad_seq_row, ';') ) {
    $pad_seq_multi_rows = pad_explode ($pad_seq_row, ';');
    $pad_seq_row = 0;
  }

  $pad_seq_values = [];
  if ( $pad_seq_value )
    $pad_seq_values = pad_explode ($pad_seq_value, ';');

  $pad_seq_checks = [];
  foreach ( $pad_parms_tag as $pad_k => $pad_v )
    if ( $pad_k <> $pad_tag and pad_file_exists ( PAD_HOME . "sequence/checks/$pad_k.php" ) ) {
      pad_set_arr_var ( 'options_done', $pad_k, TRUE );
      $GLOBALS ["pad_seq_$pad_k"] = $pad_v;
      $pad_seq_checks [] = $pad_k;
    }

  $pad_seq_actions = [];
  foreach ( $pad_parms_tag as $pad_k => $pad_v )
    if ( $pad_k <> $pad_tag and pad_file_exists ( PAD_HOME . "sequence/actions/$pad_k.php" ) ) {
      pad_set_arr_var ( 'options_done', $pad_k, TRUE );
      $GLOBALS ["pad_seq_$pad_k"] = $pad_v;
      $pad_seq_actions [] = $pad_k;
    }

  pad_set_arr_var ( 'options_done', 'random', TRUE );
  pad_set_arr_var ( 'options_done', 'rows',   TRUE ); 
  pad_set_arr_var ( 'options_done', 'row',    TRUE ); 
  pad_set_arr_var ( 'options_done', 'start',  TRUE ); 
  pad_set_arr_var ( 'options_done', 'end',    TRUE ); 
  pad_set_arr_var ( 'options_done', 'first',  TRUE ); 
  pad_set_arr_var ( 'options_done', 'last',   TRUE ); 

  $pad_seq_base   = [];
  $pad_seq_result = [];

  if ( ! $pad_seq_build )
    if ( $pad_seq_pull )
      $pad_seq_build = 'pull';
    elseif ( $pad_seq_random and pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/random.php" ) )
      $pad_seq_build = 'random';
    elseif ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/loop.php" ) )
      $pad_seq_build = 'loop';
    else
      $pad_seq_build = 'jump';

  include PAD_HOME . "sequence/build/$pad_seq_build.php";  
  include PAD_HOME . "sequence/options.php";  

  if ( $pad_seq_random and ! pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/random.php" ) )
    include PAD_HOME . "sequence/options/shuffle.php";  

  if ( $pad_seq_push ) {
    $pad_seq_store [$pad_seq_push] = $pad_seq_result;
    return NULL;
  }
  
  return $pad_seq_result; 

?>