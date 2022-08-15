<?php

  if ( ! $padSeq_random )
    if ( "padSequence_bool_$padSeq_seq"($padSeq_loop) )
      return $padSeq_loop;
    else
      return FALSE;

  $padSeq_random_try = 1;

  while ( $padSeq_random_try <= $padSeq_max ) {

    if ( count ($padSeq_for) )
      $padSeq_loop_bool = $padSeq_for [array_rand($padSeq_for)];
    else
      $padSeq_loop_bool = padSeq_random ( $padSeq_loop_start, $padSeq_loop_end );
 
    include_once PAD . "sequence/types/$padSeq_seq/bool.php";

    if ( "padSequence_bool_$padSeq_seq"($padSeq_loop_bool) )
      return $padSeq_loop_bool;
     
    $padSeq_random_try++;
    
  }
 
  return NULL;

?>