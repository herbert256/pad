<?php

  $pad_seq_rows   = $pad_parms_tag ['rows']   ?? 0;
  $pad_seq_row    = $pad_parms_tag ['row']    ?? 0;
  $pad_seq_min    = $pad_parms_tag ['min']    ?? 0;
  $pad_seq_max    = $pad_parms_tag ['max']    ?? 0;
  $pad_seq_from   = $pad_parms_tag ['from']   ?? 0;
  $pad_seq_to     = $pad_parms_tag ['to']     ?? 0;
  $pad_seq_start  = $pad_parms_tag ['start']  ?? 0;
  $pad_seq_end    = $pad_parms_tag ['end']    ?? 0;
  $pad_seq_random = $pad_parms_tag ['random'] ?? 0;
  $pad_seq_unique = $pad_parms_tag ['unique'] ?? 0;
  $pad_seq_floor  = $pad_parms_tag ['floor']  ?? 0;
  $pad_seq_ceil   = $pad_parms_tag ['ceil']   ?? 0;

  if ($pad_seq_from and ! $pad_seq_min)
    $pad_seq_min = $pad_seq_from;

  if ($pad_seq_to and ! $pad_seq_max)
    $pad_seq_max = $pad_seq_to;

  if (!$pad_seq_max)
    $pad_seq_max = PHP_INT_MAX;

  if ( isset($pad_parms_tag [$pad_tag]) )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parms_tag [$pad_tag];
  elseif ( $pad_parm )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parm;
  else
    $GLOBALS ["pad_seq_$pad_tag"] = 1;
 
  pad_set_arr_var ( 'options_done', 'random', TRUE );

  if ( isset ( $pad_parms_tag ['rows']  ) ) pad_set_arr_var ( 'options_done', 'rows',  TRUE ); 
  if ( isset ( $pad_parms_tag ['row']   ) ) pad_set_arr_var ( 'options_done', 'row',   TRUE ); 
  if ( isset ( $pad_parms_tag ['start'] ) ) pad_set_arr_var ( 'options_done', 'start', TRUE ); 
  if ( isset ( $pad_parms_tag ['end']   ) ) pad_set_arr_var ( 'options_done', 'end',   TRUE ); 

  $pad_seq_result = $pad_seq_base = [];

  if ( $pad_seq_random and $pad_tag <> 'random' and in_array($pad_tag, ['step', 'multiple', 'prime', 'power'] ) )
    $pad_seq_init = include PAD_HOME . "sequence/random.php" ;
  else 
    if ( ! pad_file_exists ( PAD_HOME . "sequence/types/$pad_tag/init.php" )) 
      $pad_seq_init = $pad_seq_min;
    else
      $pad_seq_init = include PAD_HOME . "sequence/types/$pad_tag/init.php";

  if ( is_null($pad_seq_init) or $pad_seq_init == FALSE )
    return FALSE;
 
  if ( is_array ($pad_seq_init) and ! count($pad_seq_init) )
    return FALSE;
 
  if ( ! is_array($pad_seq_init) ) {
    $pad_seq_save = $pad_seq_init;
    $pad_seq_init = [];
    $pad_seq_init [] = $pad_seq_save;
  }

  $pad_seq_idx = 1;
  $pad_seq_now = $pad_seq_init [0]; 

  while ( 1 ) {

    if ( ! ($pad_seq_unique and in_array ($pad_seq_now, $pad_seq_base) ) ) {

      $pad_seq_base [$pad_seq_idx] = $pad_seq_now;

      if ( $pad_seq_max   and $pad_seq_now   >  $pad_seq_max ) break;
      if ( $pad_seq_min   and $pad_seq_min   >  $pad_seq_now ) goto next;
      if ( $pad_seq_row   and $pad_seq_row   <> $pad_seq_idx ) goto next;
      if ( $pad_seq_start and $pad_seq_start >  $pad_seq_idx ) goto next;

      $pad_seq_result [$pad_seq_idx] = $pad_seq_now;

next:

      if ( $pad_seq_row  and $pad_seq_row  == $pad_seq_idx            ) break;
      if ( $pad_seq_rows and $pad_seq_rows == count ($pad_seq_result) ) break;
      if ( $pad_seq_end  and $pad_seq_end  == $pad_seq_idx            ) break;

      $pad_seq_idx++;

    }

    $pad_seq_now = include PAD_HOME . "sequence/next.php";

    if ( is_null($pad_seq_now) or $pad_seq_now === FALSE )
      break;
  
  }

  foreach ( $pad_parms_tag as $pad_seq_opt_name => $pad_seq_opt_value) 
    if ( $pad_seq_opt_name <> $pad_tag )
      if ( pad_file_exists ( PAD_HOME . "sequence/options/$pad_seq_opt_name.php" ) ) {
        include PAD_HOME . "sequence/options/$pad_seq_opt_name.php";
        pad_set_arr_var ( 'options_done', $pad_seq_opt_name, TRUE );
      }

  if ( $pad_seq_random and $pad_tag <> 'random' and ! in_array($pad_tag, ['step', 'multiple', 'prime', 'power'] ) ) {
    pad_set_arr_var ( 'options_done', 'random', TRUE );
    include PAD_HOME . "sequence/options/shuffle.php";
  }

  return $pad_seq_result; 

?>