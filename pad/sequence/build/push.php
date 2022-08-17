<?php

  if ( ! $padSeqPush )
    if ($padSeqUpdate )
      $padSeqPush = TRUE;
    elseif ( ! $padPair and $padSeqPull )
      $padSeqPush = TRUE;

  if ( ! $padSeqPush )
    return;

  if ( $padSeqPush === TRUE ) 
    if ( $padSeqPull and $padSeqPull !== TRUE )
      $padSeqPush = $padSeqPull;
    elseif ( $padSeqStoreGet )
      $padSeqPush = $padSeqStoreGet;
    else
      $padSeqPush = $padSeqName;

  $padSeqStore [$padSeqPush] = $padSeqResult;

?>