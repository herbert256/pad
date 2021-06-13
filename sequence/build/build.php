<?php

  $pad_seq_rows     = $pad_parms_tag ['rows']    ?? 0;
  $pad_seq_page     = $pad_parms_tag ['page']    ?? 0;
  $pad_seq_row      = $pad_parms_tag ['row']     ?? 0;
  $pad_seq_value    = $pad_parms_tag ['value']   ?? 0;

  $pad_seq_min      = $pad_parms_tag ['min']     ?? 0;
  $pad_seq_max      = $pad_parms_tag ['max']     ?? PHP_INT_MAX;

  $pad_seq_from     = $pad_parms_tag ['from']    ?? 0;
  $pad_seq_to       = $pad_parms_tag ['to']      ?? PHP_INT_MAX;

  $pad_seq_start    = $pad_parms_tag ['start']   ?? 0;
  $pad_seq_end      = $pad_parms_tag ['end']     ?? PHP_INT_MAX;

  $pad_seq_unique   = $pad_parms_tag ['unique']  ?? 0;
  $pad_seq_random   = $pad_parms_tag ['random']  ?? 0;

  $pad_seq_push     = $pad_parms_tag ['push']    ?? '';
  $pad_seq_pull     = $pad_parms_tag ['pull']    ?? ''; 
  $pad_seq_build    = $pad_parms_tag ['build']   ?? '';
  $pad_seq_loop     = $pad_parms_tag ['loop']    ?? '';

  $pad_seq_protect  = $pad_parms_tag ['protect'] ?? 10000;

  foreach ( $pad_parms_tag as $pad_k => $pad_v )
    if ( isset ( $GLOBALS ["pad_seq_$pad_k"] ) )
      pad_set_arr_var ( 'options_done', $pad_k, TRUE );

  $pad_seq_protect_cnt = $pad_seq_base_cnt = 0;

  if ( $pad_seq_page ) {
    $pad_seq_page_start = (($pad_seq_page-1) * $pad_seq_rows) + 1; 
    $pad_seq_page_end   = $pad_seq_page * $pad_seq_rows; 
  }

  if ( isset($pad_parms_tag [$pad_tag]) )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parms_tag [$pad_tag];
  elseif ( $pad_parm )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parm;
  else
    $GLOBALS ["pad_seq_$pad_tag"] = TRUE;

  $pad_seq_row   = (!$pad_seq_row)   ? [] : pad_explode ($pad_seq_row,   ';');
  $pad_seq_value = (!$pad_seq_value) ? [] : pad_explode ($pad_seq_value, ';');

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

  $pad_seq_base   = [];
  $pad_seq_result = [];

  if ( ! $pad_seq_build )
    if ( $pad_seq_loop )
      $pad_seq_build = 'loop';
    elseif ( $pad_seq_pull ) 
      $pad_seq_build = 'pull';  
    elseif ( $pad_seq_random and pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/random.php" ) ) 
      $pad_seq_build = 'random';
    elseif ( ($pad_seq_from or $pad_seq_to <> PHP_INT_MAX ) and pad_file_exists (PAD_HOME . "sequence/types/$pad_tag/from_to.php") )
      $pad_seq_build = 'loop';
    elseif ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/fixed.php" ) ) 
      $pad_seq_build = 'fixed';
    elseif ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/jump.php" ) ) 
      $pad_seq_build = 'jump';
    else                                                                                               
      $pad_seq_build = 'loop';

  include PAD_HOME . "sequence/build/types/$pad_seq_build.php";
  
  foreach ( $pad_parms_tag as $pad_seq_opt_name => $pad_seq_opt_value)
    if ( pad_file_exists ( PAD_HOME . "sequence/options/$pad_seq_opt_name.php" ) ) {
      pad_set_arr_var ( 'options_done', $pad_seq_opt_name, TRUE );
      include PAD_HOME . "sequence/options/$pad_seq_opt_name.php";
    }

  if ( !$pad_seq_pull and $pad_seq_random and !pad_file_exists(PAD_HOME . "sequence/types/$pad_tag/random.php") )
    include PAD_HOME . "sequence/options/shuffle.php";  

  if ( $pad_seq_push ) {
    if ( $pad_seq_push === TRUE )
      $pad_seq_push = 'pad_seq';
    $pad_seq_store [$pad_seq_push] = $pad_seq_result;
    return NULL;
  }
  
  return $pad_seq_result; 

?>