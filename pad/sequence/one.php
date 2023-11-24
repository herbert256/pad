<?php

  $padSeq++;

  if ( $padSeq > $padSeqProtect                          ) return FALSE;
  if ( $padSeqBuild == 'order' and $padSeq < $padSeqFrom ) return TRUE;
 
  if ( $padSeqRandom )
    $padSeqLoop = padSeqRandom ( $padSeqFor, $padSeqStart, $padSeqEnd, $padSeqInc );

  if     ( $padSeqBuild == 'fixed'    ) $padSequence = $padSeqLoop;
  elseif ( $padSeqBuild == 'function' ) $padSequence = ( 'padSeq'     . ucfirst($padSeqSeq) ) ($padSeqLoop);
  elseif ( $padSeqBuild == 'bool'     ) $padSequence = ( 'padSeqBool' . ucfirst($padSeqSeq) ) ($padSeqLoop);
  else                                  $padSequence = include "$padSeqType/$padSeqBuild.php";

  if     ( $padSequence === FALSE ) return TRUE;   
  elseif ( $padSequence === NULL  ) return FALSE;
  elseif ( $padSequence === INF   ) return FALSE; 
  elseif ( $padSequence === NAN   ) return FALSE; 
  elseif ( $padSequence === TRUE  ) $padSequence = $padSeqLoop;

  if ( count ($padSeqOprGo) ) {
    $padSequence = include pad . 'sequence/operations/operations.php';
    if ( $padSequence === TRUE ) 
      return TRUE;
  }

  $padSeqBase++;

  if ( is_numeric ($padSequence) and $padSequence < $padSeqMin  ) return TRUE;
  if ( is_numeric ($padSequence) and $padSequence > $padSeqMax  ) return $padSeqRandom;
  if ( $padSeqUnique and in_array ($padSequence, $padSeqResult) ) return TRUE;
  if ( $padSeqSkip and $padSeqBase <= $padSeqSkip )               return TRUE;

  $padSeqResult [] = $padSequence;

  if ( $padSeqRows  and count($padSeqResult) >= $padSeqRows )     return FALSE;
  if ( $padSeqCheck and count($padSeqResult) >= $padSeqSave )     return FALSE;

  return TRUE;

?>