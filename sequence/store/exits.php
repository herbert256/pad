<?php

  $padSeqResultSave = $padSeqResult;

  $padSeqSeqSave    = $padSeqSeq;
  $padSeqBuildSave  = $padSeqBuild;
  $padSeqParmSave   = $padSeqParm;
  $padSeqLoopSave   = $padSeqLoop;
  $padSeqNameSave   = $padSeqName;

  foreach ( $padSeqStoreList as $padSeqStoreItem ) {

    extract ( $padSeqStoreItem );

    if ( $padSeqStoreType == 'operation' ) include '/pad/sequence/store/exitOperation.php';
    else                                   include '/pad/sequence/store/exitAction.php';  
  
    $padSeqStore [$padSeqStoreName] = $padSeqResult;

    $padSeqResult = $padSeqResultSave;

  }
  
  $padSeqSeq   = $padSeqSeqSave;
  $padSeqBuild = $padSeqBuildSave;
  $padSeqLoop  = $padSeqLoopSave;
  $padSeqParm  = $padSeqParmSave;
  $padSeqName  = $padSeqNameSave;
   
?>