<?php

  $padSeqOprParmSave = $padSeqParm;
  $padSeqOprLoopSave = $padSeqLoop;
  $padSeqOprSeq      = $padSequence;
  $padSeqOprLst      = $padSequence;

  foreach ( $padSeqOprGo as $padSeqOprName => $padSeqOprVal ) {

    padSeqSet ( $padSeqOprName, $padSeqOprVal );

    if ( in_array ( $padSeqOprName, $padSeqOpr ) ) 
      $padSeqOprSeq = include pad . 'sequence/operations/mkr.php';
    else
      $padSeqOprSeq = include pad . 'sequence/operations/other.php';

    if ( $padSeqOprSeq === TRUE ) {
      $padSeqParm = $padSeqOprParmSave;
      $padSeqLoop = $padSeqOprLoopSave;
      return TRUE;
    }

   }

  $padSeqParm = $padSeqOprParmSave;
  $padSeqLoop = $padSeqOprLoopSave;

  return $padSeqOprSeq;

?>