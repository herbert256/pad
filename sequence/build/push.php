<?php

  if ( ! $padSeqPush and ( $padSeqUpdate or ! $padPair[$pad] ) )
    $padSeqPush = TRUE;

  if ( ! $padSeqPush )
    return;

  if ( $padSeqPush === TRUE ) 
    if ( isset ( $padPrm [$pad] ['name'] ) )
      $padSeqPush = $padPrm[$pad]['name']; 
    elseif ( $padSeqPull and $padSeqPull !== TRUE )
      $padSeqPush = $padSeqPull;
    elseif ( $padSeqStoreGet )
      $padSeqPush = $padSeqStoreGet;
    else
      $padSeqPush = $padSeqName;

  $padSeqStore [$padSeqPush] = $padSeqResult;

?>