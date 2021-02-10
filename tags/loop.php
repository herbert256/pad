<?php

  if ( isset($pad_parms_tag ['walk']) )
    $pad_parms_tag ['type'] = 'walk';

  $pad_loop_type  = $pad_parms_tag ['type']  ?? 'build';
  $pad_loop_start = $pad_parms_tag ['start'] ?? 1;
  $pad_loop_end   = $pad_parms_tag ['end']   ?? 10;
  $pad_loop_step  = $pad_parms_tag ['step']  ?? 1;

  $pad_options_done ['start'] = TRUE;
  $pad_options_done ['end']   = TRUE;

  pad_trace ('loop', "$pad_loop_type / $pad_loop_start / $pad_loop_end / $pad_loop_step");

  if ( $pad_loop_type == 'build') {
    $pad_loop = []; 
    foreach (range($pad_loop_start, $pad_loop_end, $pad_loop_step) as $pad_loop_number) 
      $pad_loop [] = $pad_loop_number;
    return $pad_loop;
  }

  if ( $pad_walk == 'start' )
    $pad_loop_now [$pad_lvl] = $pad_loop_start;
  else
    $pad_loop_now [$pad_lvl] += $pad_loop_step;

  if ( $pad_loop_now [$pad_lvl] > $pad_loop_end ) {  
    $pad_walk = '';
    return NULL;
  }

  $pad_walk = 'next';

  return  [ 0 => [ $pad_name => $pad_loop_now[$pad_lvl] ] ];

?>