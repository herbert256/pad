<?php

  if ( ! $padSeqPush )
    if ($padSeqUpdate )
      $padSeqPush = TRUE;
    elseif ( ! $padPair[$pad] and $padSeqPull )
      $padSeqPush = TRUE;

  if ( ! $padSeqPush and ! $padPair[$pad] )
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