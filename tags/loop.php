<?php

  $pad_data_name = $pad_name;

  if ( $pad_parm ) {

    $pad_loop_range = pad_explode ($pad_parms, '..');

    if ( count ($pad_loop_range) == 2 )
      return range ( $pad_loop_range[0], $pad_loop_range[1] );

    if ( ctype_digit($pad_parm) )
      return range ( 1, intval($data, 10) );
 
  }

  $pad_loop       = [];
  $pad_loop_type  = $pad_parms_pad ['type']  ?? 'build';
  $pad_loop_start = $pad_parms_pad ['start'] ?? 1;
  $pad_loop_end   = $pad_parms_pad ['end']   ?? 10;
  $pad_loop_step  = $pad_parms_pad ['step']  ?? 1;

  if ( $pad_walk == 'start' )
    $pad_loop_now [$pad_lvl] = $pad_loop_start;

  if ( $pad_loop_type == 'build') {

    while ( $pad_loop_now [$pad_lvl] <= $pad_loop_end ) {
      $pad_loop [$pad_loop_now[$pad_lvl]] [$pad_name] = $pad_loop_now[$pad_lvl];
      $pad_loop_now [$pad_lvl] += $pad_loop_step;
    } 

    $pad_data_name = $pad_name;
    return $pad_loop;

  }
  
  if ( $pad_loop_now [$pad_lvl] <= $pad_loop_end ) {
    $pad_walk   = 'next';
    $pad_loop [$pad_loop_now[$pad_lvl]] [$pad_name] = $pad_loop_now[$pad_lvl];
    $pad_return = $pad_loop ;
  } else {
    $pad_walk   = '';
    $pad_return = NULL;
  }
      
  $pad_loop_now [$pad_lvl] += $pad_loop_step;

  return $pad_return;

?>