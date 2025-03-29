<?php

  if ( isset ( $padPrm [$pad] ['update'] ) and $padSeqPull )
    $padSeqStore [$padSeqPull] = array_values ( $padSeqResult );

  if ( $padSeqStoreName ) {

    $padSeqNames [] = $padSeqStoreName;

    if ( $padSeqStoreName and $padSeqStoreName !== TRUE )
      if ( $padSeqStoreUpdated ) $padSeqStore [$padSeqStoreName] = $padSeqStore [$padSeqPull];
      else                       $padSeqStore [$padSeqStoreName] = array_values ( $padSeqResult );

  } elseif ( ! $padPair [$pad] ) {

    if ( $padSeqName and $padSeqName !== TRUE )
      if ( $padSeqStoreUpdated ) $padSeqStore [$padSeqName] = $padSeqStore [$padSeqPull];
      else                       $padSeqStore [$padSeqName] = array_values ( $padSeqResult );

  }

?>