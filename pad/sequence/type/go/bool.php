<?php

  if ( ! $pad_seq_random )
    if ( "pad_sequence_$pad_seq_seq"($pad_sequence) )
      return $pad_sequence;
    else
      return FALSE;

  $pad_seq_random_try = 1;

  while ( $pad_seq_random_try <= $pad_seq_loop_max ) {

    if ( "pad_sequence_$pad_seq_seq"($pad_sequence) )
      return $pad_sequence;

    $pad_sequence = pad_seq_random ( $pad_seq_loop_start, $pad_seq_loop_end );
     
    $pad_seq_random_try++;
    
  }
 
  return NULL;

?>  