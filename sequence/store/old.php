<?php

  $padSeqSeqSave    = $padSeqSeq;
  $padSeqResultSave = $padSeqResult;

  foreach ( $padSeqStoreList as $padSeqStoreItem ) {

    extract ( $padSeqStoreItem );

    if ( $padSeqStoreType == 'operation' ) include '/pad/sequence/store/exitOperation.php';
    else                                   include '/pad/sequence/store/exitAction.php';  
  
    $padSeqStore [$padSeqStoreName] = array_values ( $padSeqResult );

    $padSeqResult = $padSeqResultSave;

  }
  
  $padSeqSeq = $padSeqSeqSave;
   
?>