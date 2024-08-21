<?php
 
  if ( ! count ( $padSeqOperations ) ) 
    return $padSeq;

  $padSeqSeqSave   = $padSeqSeq;
  $padSeqBuildSave = $padSeqBuild;
  $padSeqParmSave  = $padSeqParm;
  $padSeqLoopSave  = $padSeqLoop;
  $padSeqNameSave  = $padSeqName;

  $padSeq = include '/pad/sequence/operations/go.php';

  $padSeqSeq   = $padSeqSeqSave;
  $padSeqBuild = $padSeqBuildSave;
  $padSeqLoop  = $padSeqLoopSave;
  $padSeqParm  = $padSeqParmSave;
  $padSeqName  = $padSeqNameSave;

  return $padSeq;

?>