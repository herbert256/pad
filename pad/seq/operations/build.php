<?php
 
  $padSeqSeqSave   = $padSeqSeq;
  $padSeqBuildSave = $padSeqBuild;
  $padSeqParmSave  = $padSeqParm;
  $padSeqLoopSave  = $padSeqLoop;

  $padSeq = include 'seq/operations/go.php';

  $padSeqSeq   = $padSeqSeqSave;
  $padSeqBuild = $padSeqBuildSave;
  $padSeqLoop  = $padSeqLoopSave;
  $padSeqParm  = $padSeqParmSave;

  return $padSeq;

?>