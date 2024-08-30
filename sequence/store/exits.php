<?php

  $padSeqResultSave = $padSeqResult;

  $padSeqSeqSave    = $padSeqSeq;
  $padSeqBuildSave  = $padSeqBuild;
  $padSeqParmSave   = $padSeqParm;
  $padSeqLoopSave   = $padSeqLoop;
  $padSeqNameSave   = $padSeqName;

  foreach ( $padSeqStoreList as $padSeqStoreItem ) {

    extract ( $padSeqStoreItem );

    if ( $padSeqStoreType == 'operation' ) include '/pad/store/exitOperation.php';
    else                                   include '/pad/store/exitAction.php';  
  
    $padSeqStore [$padSeqStoreName] = $padSeqResult;

    $padSeqResult = $padSeqResultSave;

  }
  
  $padSeqSeq   = $padSeqSeqSave;
  $padSeqBuild = $padSeqBuildSave;
  $padSeqLoop  = $padSeqLoopSave;
  $padSeqParm  = $padSeqParmSave;
  $padSeqName  = $padSeqNameSave;
   
?>