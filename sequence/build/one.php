<?php
 
  $padSeqTries++;

  if ( $padSeqTries > $padSeqTry                              ) return FALSE;
  if ( $padSeqBuild == 'order' and $padSeqTries < $padSeqFrom ) return TRUE;
 
  if ( $padSeqRandom )
    $padSeqLoop = padSeqRandom ( $padSeqFixed, $padSeqStart, $padSeqEnd, $padSeqInc );

  if   ( $padSeqBuild == 'fixed') $padSeq = $padSeqLoop;
  else                            $padSeq = include '/pad/sequence/build/call.php';

  if     ( $padSeq === FALSE ) return TRUE;
  elseif ( $padSeq === TRUE  ) $padSeq = $padSeqLoop;

  $padSeq = include '/pad/sequence/operations/build.php';
  if ( $padSeq === FALSE ) 
    return TRUE;
  
  $padSeqBase++;

  if ( is_numeric ($padSeq) and $padSeq < $padSeqMin       ) return TRUE;
  if ( is_numeric ($padSeq) and $padSeq > $padSeqMax       ) return $padSeqRandom;
  if ( $padSeqUnique and in_array ($padSeq, $padSeqResult) ) return TRUE;
  if ( $padSeqSkip and $padSeqBase <= $padSeqSkip )          return TRUE;

  $padSeqResult [] = $padSeq;

  if ( $padSeqRows  and count($padSeqResult) >= $padSeqRows ) return FALSE;
  if ( $padSeqCheck and count($padSeqResult) >= $padSeqMost  ) return FALSE;

  return TRUE;

?>