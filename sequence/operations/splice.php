<?php

  if ( count ($padSeqActionList) == 2 ) $padSeqStoreName = $padSeqActionList [1];
  else                                  $padSeqStoreName = $padSeqActionList [2];

  if ( count ($padSeqActionList) == 2 ) {
    $padSeqActionList [1] = NULL;
    $padSeqActionList [2] = $padSeqStoreName; 
  }

  $padSeqStoreTemp                = $padSeqResult;
  $padSeqResult                   = $padSeqStore [$padSeqStoreName];
  $padSeqStore [$padSeqStoreName] = $padSeqStoreTemp;

?>