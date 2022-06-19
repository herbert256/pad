<?php

  if ( ! $pad_seq_random )
    if ( "pad_sequence_$pad_seq_seq"($pad_seq_loop_idx) )
      return $pad_seq_loop_idx;
    else
      return FALSE;

  $pad_seq_random_try = 1;

  while ( $pad_seq_random_try <= $pad_seq_loop_max ) {

    if ( count ($pad_seq_for) )
      $pad_seq_loop_bool = $pad_seq_for [array_rand($pad_seq_for)];
    else
      $pad_seq_loop_bool = pad_seq_random ( $pad_seq_loop_start, $pad_seq_loop_end );
 
    if ( "pad_sequence_$pad_seq_seq"($pad_seq_loop_bool) )
      return $pad_seq_loop_bool;
     
    $pad_seq_random_try++;
    
  }
 
  return NULL;

?>  