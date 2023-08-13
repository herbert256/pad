<?php

  $padSeq++;

  if ( $padSeq > $padSeqProtect ) 
    return FALSE;
 
  $padSequence = include pad . 'sequence/type/go/sequence.php';

  if     ( $padSequence === TRUE  ) return TRUE;
  elseif ( $padSequence === FALSE ) return FALSE;   

  if ( $padSeqBuild == 'order' and $padSeq < $padSeqFrom ) 
    return TRUE;

  if ( count ($padSeqOprGo) ) {
    $padSequence = include pad . 'sequence/type/go/operations.php';
    if ( $padSequence === TRUE ) 
      return TRUE;
  }

  if ( is_numeric ($padSequence) and $padSequence < $padSeqMin ) 
    return TRUE;
  
  if ( is_numeric ($padSequence) and $padSequence > $padSeqMax ) 
    return ( isset ( $padPrm [$pad] ['random'] ) ) ;
 
  if ( $padSeqUnique and in_array ($padSequence, $padSeqResult) ) 
    return TRUE;

  $padSeqBase++;

  if ( $padSeqPage and $padSeqBase < $padSeqPageStart ) 
    return TRUE; 

  $padSeqResult [] = $padSequence;

  if ( $padSeqRows and count($padSeqResult) >= $padSeqRows ) 
    return FALSE;

  if ( ! $padSeqRows and $padSeqTo == PHP_INT_MAX and $padSeqMax == PHP_INT_MAX and count($padSeqResult) >= $padSeqSave )
    return FALSE;

  return TRUE;

?>