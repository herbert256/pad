<?php

 if ( !$pad_seq_value and !$pad_seq_rows and !$pad_seq_row and 
    $pad_seq_to == PHP_INT_MAX and $pad_seq_max == PHP_INT_MAX  and $pad_seq_end == PHP_INT_MAX )
    $pad_seq_rows = 100;

  $pad_seq_init = $pad_seq_base = $pad_seq_result = $pad_seq_prepare = [];
  $pad_seq_cnt  = $pad_seq_protect_cnt = 0;

  for ( $pad_seq_idx = 0; $pad_seq_idx <= 19; $pad_seq_idx++ ) 
    $GLOBALS [ 'pad_seq_sts_' . sprintf('%02d', $pad_seq_idx) ] = 0;

  include 'build/checks.php';
  include 'build/actions.php';
  include 'build/increment.php';
  include 'build/from_to.php';
  include 'build/min_max.php';
  include 'build/start_end.php';
  include 'build/loop_idx_end.php';
  include 'build/init_exit.php';
  include 'build/page.php';
  include 'build/pull.php';
  #include 'build/rows.php';

  $pad_seq_max_loops = ($pad_seq_end - $pad_seq_from ) + 1;

  if ( isset($pad_parms_tag [$pad_tag]) )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parms_tag [$pad_tag];
  elseif ( $pad_parm )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parm;
  else
    $GLOBALS ["pad_seq_$pad_tag"] = TRUE;

  $pad_seq_row   = (!$pad_seq_row)   ? [] : pad_explode ($pad_seq_row,   ';'); 
  $pad_seq_value = (!$pad_seq_value) ? [] : pad_explode ($pad_seq_value, ';');

  $pad_seq_build = include 'build/type.php';  

  if ( $pad_seq_build == 'function' ) 
    include_once "functions/$pad_tag.php";

  if ( $pad_pull_start ) 
    include "type/pull.php";
  else
    include "type/$pad_seq_build.php";

  include 'build/options.php';
  
  if ( !$pad_seq_pull and $pad_seq_random and !pad_file_exists(PAD_HOME . "pad/sequence/types/$pad_tag/random.php") )
    include PAD_HOME . 'pad/sequence/options/shuffle.php';  

  if ( $pad_seq_push )
    return include 'build/push.php';

  return $pad_seq_result; 

?>