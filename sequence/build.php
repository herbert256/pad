<?php

  if ( !$pad_seq_value and !$pad_seq_rows and !$pad_seq_row and 
    $pad_seq_to == PHP_INT_MAX and $pad_seq_max == PHP_INT_MAX  and $pad_seq_end == PHP_INT_MAX )
    $pad_seq_rows = 100;

  $pad_seq_init = $pad_seq_base = $pad_seq_result = $pad_seq_prepare = [];
  $pad_seq_cnt  = $pad_seq_protect_cnt = 0;

  for ( $pad_seq_idx = 0; $pad_seq_idx <= 18; $pad_seq_idx++ ) 
    $GLOBALS [ 'pad_seq_sts_' . sprintf('%02d', $pad_seq_idx) ] = 0;

  include 'increment.php';
  include 'from_to.php';
  include 'min_max.php';
  include 'loop_idx_end.php';
  include 'init_exit.php';
  include 'page.php';
  include 'pull.php';

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

  $pad_seq_jump  = ($pad_seq_build == 'jump' );
  $pad_seq_fixed = ($pad_seq_build == 'fixed');

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