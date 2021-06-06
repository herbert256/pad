<?php

  $pad_seq_rows   = $pad_parms_tag ['rows']  ?? 0;
  $pad_seq_row    = $pad_parms_tag ['row']   ?? 0;
  $pad_seq_min    = $pad_parms_tag ['min']   ?? 0;
  $pad_seq_max    = $pad_parms_tag ['max']   ?? 0;
  $pad_seq_from   = $pad_parms_tag ['from']  ?? 0;
  $pad_seq_to     = $pad_parms_tag ['to']    ?? 0;
  $pad_seq_start  = $pad_parms_tag ['start'] ?? 0;
  $pad_seq_end    = $pad_parms_tag ['end']   ?? 0;

  if ($pad_seq_from and ! $pad_seq_min)
    $pad_seq_min = $pad_seq_from;

  if ($pad_seq_to and ! $pad_seq_max)
    $pad_seq_max = $pad_seq_to;

  if ( isset($pad_parms_tag [$pad_tag]) )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parms_tag [$pad_tag];
  elseif ( $pad_parm )
    $GLOBALS ["pad_seq_$pad_tag"] = $pad_parm;
  else
    $GLOBALS ["pad_seq_$pad_tag"] = 1;
 
  if ( isset ( $pad_parms_tag ['rows']  ) ) pad_set_arr_var ( 'options_done', 'rows',  TRUE ); 
  if ( isset ( $pad_parms_tag ['row']   ) ) pad_set_arr_var ( 'options_done', 'row',   TRUE ); 
  if ( isset ( $pad_parms_tag ['start'] ) ) pad_set_arr_var ( 'options_done', 'start', TRUE ); 
  if ( isset ( $pad_parms_tag ['end']   ) ) pad_set_arr_var ( 'options_done', 'end',   TRUE ); 

  $pad_seq_result = $pad_seq_base = [];

  if ( ! pad_file_exists ( PAD_HOME . "sequence/$pad_tag/init.php" )) 

    $pad_seq_init = $pad_seq_min;

  else {

    $pad_seq_init = include PAD_HOME . "sequence/$pad_tag/init.php";

    if ( is_null($pad_seq_init) )
      return FALSE;

  }
 
  if ( ! is_array($pad_seq_init) ) {
    $pad_seq_save = $pad_seq_init;
    $pad_seq_init = [];
    $pad_seq_init [] = $pad_seq_save;
  }

  if ( ! count($pad_seq_init) )
    return FALSE;

  $pad_seq_idx = 1;
  $pad_seq_now = $pad_seq_init [0]; 

  while ( 1 ) {

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

    if ( isset ( $pad_seq_init [$pad_seq_idx] ) ) 

      $pad_seq_now = $pad_seq_init [$pad_seq_idx];

    else {
   
      if ( ! pad_file_exists ( PAD_HOME . "sequence/$pad_tag/row.php" ))
        break;

      $G = &$pad_seq_base;
      $n = $pad_seq_idx;

      $pad_seq_now = include PAD_HOME . "sequence/$pad_tag/row.php";

      if ( is_null($pad_seq_now) )
        break;
  
    }

    $pad_seq_idx++;
  
  }

  return $pad_seq_result; 

?>