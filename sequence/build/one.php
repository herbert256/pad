<?php
 
  $padSeq++;

  if ( $padSeq > $padSeqProtect                          ) return FALSE;
  if ( $padSeqBuild == 'order' and $padSeq < $padSeqFrom ) return TRUE;
 
  if ( $padSeqRandom )
    $padSeqLoop = padSeqRandom ( $padSeqFixed, $padSeqStart, $padSeqEnd, $padSeqInc );

  if     ( $padSeqBuild == 'fixed'    ) $padSeqOne = $padSeqLoop;
  elseif ( $padSeqBuild == 'function' ) $padSeqOne = ( 'padSeq'     . ucfirst($padSeqSeq) ) ( $padSeqLoop );
  elseif ( $padSeqBuild == 'bool'     ) $padSeqOne = ( 'padSeqBool' . ucfirst($padSeqSeq) ) ( $padSeqLoop );
  else                                  $padSeqOne = include "/pad/sequence/types/$padSeqSeq/$padSeqBuild.php";

  if     ( $padSeqOne === FALSE ) return TRUE;   
  elseif ( $padSeqOne === NULL  ) return FALSE;
  elseif ( $padSeqOne === INF   ) return FALSE; 
  elseif ( $padSeqOne === NAN   ) return FALSE; 
  elseif ( $padSeqOne === TRUE  ) $padSeqOne = $padSeqLoop;

  if ( count ( $padSeqOprList ) ) {
    $padSeqParmSave = $padSeqParm;
    $padSeqLoopSave = $padSeqLoop;
    $padSeqOne = include '/pad/sequence/build/operations.php';
    $padSeqLoop = $padSeqLoopSave;
    $padSeqParm = $padSeqParmSave;
    if ( $padSeqOne === FALSE ) 
      return TRUE;
  }

  $padSeqBase++;

  if ( is_numeric ($padSeqOne) and $padSeqOne < $padSeqMin    ) return TRUE;
  if ( is_numeric ($padSeqOne) and $padSeqOne > $padSeqMax    ) return $padSeqRandom;
  if ( $padSeqUnique and in_array ($padSeqOne, $padSeqResult) ) return TRUE;
  if ( $padSeqSkip and $padSeqBase <= $padSeqSkip )             return TRUE;

  $padSeqResult [] = $padSeqOne;

  if ( $padSeqRows  and count($padSeqResult) >= $padSeqRows )   return FALSE;
  if ( $padSeqCheck and count($padSeqResult) >= $padSeqSave )   return FALSE;

  return TRUE;

?>