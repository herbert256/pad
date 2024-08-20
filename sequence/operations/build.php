<?php
 
  if ( ! count ( $padSeqOperations ) ) 
    return;

  $padSeqSeqSave   = $padSeqSeq;
  $padSeqBuildSave = $padSeqBuild;
  $padSeqParmSave  = $padSeqParm;
  $padSeqLoopSave  = $padSeqLoop;
  $padSeqNameSave  = $padSeqName;

  include '/pad/sequence/operations/go.php';

  $padSeqSeq   = $padSeqSeqSave;
  $padSeqBuild = $padSeqBuildSave;
  $padSeqLoop  = $padSeqLoopSave;
  $padSeqParm  = $padSeqParmSave;
  $padSeqName  = $padSeqNameSave;

?>