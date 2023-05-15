<?php


  $padSeqBoolFunction = 'padSeqBool' . ucfirst($padSeqSeq);

  if ( ! $padSeqRandom )
    if ( $padSeqBoolFunction ($padSeqLoop) )
      return $padSeqLoop;
    else
      return FALSE;

  $padSeqRandomTry = 1;

  while ( $padSeqRandomTry <= $padSeqMax ) {

    if ( count ($padSeqFor) )
      $padSeqLoopBool = $padSeqFor [array_rand($padSeqFor)];
    else
      $padSeqLoopBool = padSeqRandom ( $padSeqLoopStart, $padSeqLoopEnd );
 
    include_once pad . "sequence/types/$padSeqSeq/bool.php";

    if ( $padSeqBoolFunction ($padSeqLoopBool) )
      return $padSeqLoopBool;
     
    $padSeqRandomTry++;
    
  }
 
  return NULL;

?>