<?php

  if ( count ($padSeqActionList) == 2 )
    $padSeqActionList [2] = NULL;

  $padSeqStoreTemp                = $padSeqResult;
  $padSeqResult                   = $padSeqStore [$padSeqPushName];
  $padSeqStore [$padSeqPushName] = $padSeqStoreTemp;

?>