<?php

  if ( ! $pSeq_random )
    if ( "pSequence_bool_$pSeq_seq"($pSeq_loop) )
      return $pSeq_loop;
    else
      return FALSE;

  $pSeq_random_try = 1;

  while ( $pSeq_random_try <= $pSeq_max ) {

    if ( count ($pSeq_for) )
      $pSeq_loop_bool = $pSeq_for [array_rand($pSeq_for)];
    else
      $pSeq_loop_bool = pSeq_random ( $pSeq_loop_start, $pSeq_loop_end );
 
    include_once PAD . "sequence/types/$pSeq_seq/bool.php";

    if ( "pSequence_bool_$pSeq_seq"($pSeq_loop_bool) )
      return $pSeq_loop_bool;
     
    $pSeq_random_try++;
    
  }
 
  return NULL;

?>