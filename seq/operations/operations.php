<?php

  $padSeqOprParmSave = $padSeqParm;
  $padSeqOprLoopSave = $padSeqLoop;
  $padSeqOprSeq      = $padSeqOne;
  $padSeqOprLst      = $padSeqOne;

  foreach ( $padSeqOprGo as $padSeqOprName => $padSeqOprVal ) {

    padSeqSet ( $padSeqOprName, $padSeqOprVal );

    if ( in_array ( $padSeqOprName, $padSeqOpr ) ) 
      $padSeqOprSeq = include '/pad/seq/operations/mkr.php';
    else
      $padSeqOprSeq = include '/pad/seq/operations/other.php';

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