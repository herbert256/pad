<?php

  foreach ( $padSeqStoreList as $padSeqActionName => $padSeqActionValue ) {

    $padSeqActionList = [$padSeqName];

    if ( ! $padSeqActionValue )
      $padSeqActionValue = $padSeqName;

    $padSeqStoreTemp           = $padSeqResult;
    $padSeqResult              = $padSeqStore [$padSeqName];
    $padSeqStore [$padSeqName] = $padSeqStoreTemp;

    include "/pad/sequence/store/types/$padSeqActionName.php";

  }
  
?>