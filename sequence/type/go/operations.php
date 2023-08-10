<?php

  $padSeqParmSave = $padSeqParm;
  $padSeqLoopSave = $padSeqLoop;

  $padSeqOprSeq = $padSequence;
  $padSeqOprLst = $padSequence;

  foreach ( $padSeqOprGo as $padSeqOprName => $padSeqOprVal ) {

    padSeqSet ( $padSeqOprName, $padSeqOprVal );

    if ( in_array ( $padSeqOprName, $padSeqOpr ) ) 
      $padSeqOprSeq = include pad . 'sequence/type/go/operations/mkr.php';
    else
      $padSeqOprSeq = include pad . 'sequence/type/go/operations/other.php';

    if ( $padSeqOprSeq === TRUE ) 
      return TRUE;

   }

  $padSeqParm = $padSeqParmSave;
  $padSeqLoop = $padSeqLoopSave;

  return $padSeqOprSeq;

?>