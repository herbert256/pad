<?php
 
  if ( is_numeric ($padSeqLoop) and $padSeqLoop > PHP_INT_MAX )
    return FALSE;

  $padSeqTries++;

  if ( $padSeqTries > $padSeqTry                              ) return FALSE;
  if ( $padSeqBuild == 'order' and $padSeqTries < $padSeqFrom ) return TRUE;
 
  if ( $padSeqRandom )
    $padSeqLoop = padSeqRandom ( $padSeqFixed, $padSeqStart, $padSeqEnd, $padSeqInc );

  if   ( $padSeqBuild == 'fixed' ) $padSeq = $padSeqLoop;
  else                             $padSeq = include PAD . 'seq/build/call.php';

  if     ( $padSeq === FALSE ) return TRUE;
  elseif ( $padSeq === TRUE  ) $padSeq = $padSeqLoop;

  if ( count ( $padSeqOperations ) ) {
    $padSeq = include PAD . 'seq/operations/build.php';
    if ( $padSeq === FALSE ) 
      return TRUE;
  }

  $padSeqBase++;

  if ( is_float ($padSeq)   and $padSeq < PHP_INT_MIN      ) return FALSE;
  if ( is_float ($padSeq)   and $padSeq > PHP_INT_MAX      ) return FALSE; 
  if ( is_numeric ($padSeq) and $padSeq < $padSeqMin       ) return TRUE;
  if ( is_numeric ($padSeq) and $padSeq > $padSeqMax       ) return TRUE;
  if ( $padSeqUnique and in_array ($padSeq, $padSeqResult) ) return TRUE;
  if ( $padSeqSkip and $padSeqBase <= $padSeqSkip )          return TRUE;

  $padSeqResult [] = $padSeq;

  if ( is_numeric ($padSeq) and $padSeq >= $padSeqStop     ) return FALSE;
  if ( $padSeqRows and count($padSeqResult) >= $padSeqRows ) return FALSE;

  return TRUE;

?>