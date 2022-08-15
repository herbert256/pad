<?php

  if ( ! $padSeqRandom )
    if ( "padSequence_bool_$padSeqSeq"($padSeqLoop) )
      return $padSeqLoop;
    else
      return FALSE;

  $padSeqRandomTry = 1;

  while ( $padSeqRandomTry <= $padSeqMax ) {

    if ( count ($padSeqFor) )
      $padSeqLoopBool = $padSeqFor [array_rand($padSeqFor)];
    else
      $padSeqLoopBool = padSeq_random ( $padSeqLoopStart, $padSeqLoopEnd );
 
    include_once PAD . "sequence/types/$padSeqSeq/bool.php";

    if ( "padSequence_bool_$padSeqSeq"($padSeqLoopBool) )
      return $padSeqLoopBool;
     
    $padSeqRandomTry++;
    
  }
 
  return NULL;

?>