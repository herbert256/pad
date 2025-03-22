<?php

  if ( $padSeqStoreName ) {

    $padSeqNames [] = $padSeqStoreName;

    if ( $padSeqStoreUpdated ) $padSeqStore [$padSeqStoreName] = $padSeqStore [$padSeqPull];
    else                       $padSeqStore [$padSeqStoreName] = array_values ( $padSeqResult );

  } elseif ( ! $padPair [$pad] ) {

    if ( $padSeqStoreUpdated ) $padSeqStore [$padSeqName] = $padSeqStore [$padSeqPull];
    else                       $padSeqStore [$padSeqName] = array_values ( $padSeqResult );

  }

?>