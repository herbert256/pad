<?php

  if ( ! $pad_seq_random )
    if ( "pad_sequence_bool_$pad_seq_seq"($pad_seq_loop) )
      return $pad_seq_loop;
    else
      return FALSE;

  $pad_seq_random_try = 1;

  while ( $pad_seq_random_try <= $pad_seq_max ) {

    if ( count ($pad_seq_for) )
      $pad_seq_loop_bool = $pad_seq_for [array_rand($pad_seq_for)];
    else
      $pad_seq_loop_bool = pad_seq_random ( $pad_seq_start, $pad_seq_end );
 
    include_once PAD . "sequence/types/$pad_seq_seq/bool.php";

    if ( "pad_sequence_bool_$pad_seq_seq"($pad_seq_loop_bool) )
      return $pad_seq_loop_bool;
     
    $pad_seq_random_try++;
    
  }
 
  return NULL;

?>