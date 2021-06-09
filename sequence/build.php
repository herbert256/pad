<?php

  $pad_seq_rows     = $pad_parms_tag ['rows']     ?? 0;
  $pad_seq_row      = $pad_parms_tag ['row']      ?? 0;
  $pad_seq_min      = $pad_parms_tag ['from']     ?? 1;
  $pad_seq_max      = $pad_parms_tag ['to']       ?? 0;
  $pad_seq_start    = $pad_parms_tag ['start']    ?? 0;
  $pad_seq_end      = $pad_parms_tag ['end']      ?? 0;
  $pad_seq_random   = $pad_parms_tag ['random']   ?? 0;
  $pad_seq_unique   = $pad_parms_tag ['unique']   ?? 0;
  $pad_seq_floor    = $pad_parms_tag ['floor']    ?? 0;
  $pad_seq_ceil     = $pad_parms_tag ['ceil']     ?? 0;
  $pad_seq_prime    = $pad_parms_tag ['prime']    ?? 0;
  $pad_seq_power    = $pad_parms_tag ['power']    ?? 0;
  $pad_seq_step     = $pad_parms_tag ['step']     ?? 0;
  $pad_seq_multiple = $pad_parms_tag ['multiple'] ?? 0;

  if ( ! $pad_seq_max )
    $pad_seq_max = PHP_INT_MAX;

  if ( isset($pad_parms_tag [$pad_tag]) )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parms_tag [$pad_tag];
  elseif ( $pad_parm )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parm;
  else
    $GLOBALS ["pad_seq_$pad_tag"] = 1;

  include PAD_HOME . "sequence/checks.php";

  pad_set_arr_var ( 'options_done', 'random', TRUE );
  pad_set_arr_var ( 'options_done', 'rows',   TRUE ); 
  pad_set_arr_var ( 'options_done', 'row',    TRUE ); 
  pad_set_arr_var ( 'options_done', 'start',  TRUE ); 
  pad_set_arr_var ( 'options_done', 'end',    TRUE ); 

  $pad_seq_base   = [];
  $pad_seq_result = [];

  if ( ($pad_tag == 'random' or $pad_seq_random) and pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/random.php" ) )
    include PAD_HOME . "sequence/random.php";
  elseif ( pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/loop.php" ) )
    include PAD_HOME . "sequence/loop.php";
  else
    include PAD_HOME . "sequence/jump.php";

  include PAD_HOME . "sequence/options.php";  

  if ( $pad_seq_random and ! pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/random.php" ) )
    include PAD_HOME . "sequence/options/shuffle.php";  

  return $pad_seq_result; 

?>