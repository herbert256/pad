<?php

  if ( isset ( $padPrm [$pad] ['update'] ) and $padSeqPull )
    padSeqStore ( $padSeqPull );

  if ( $padSeqStoreName ) {

    $padSeqNames [] = $padSeqStoreName;

    if ( $padSeqStoreName and $padSeqStoreName !== TRUE )
      if ( $padSeqStoreUpdated ) padSeqStore ( $padSeqStoreName );
      else                       padSeqStore ( $padSeqStoreName );

  } elseif ( ! $padPair [$pad] ) {

    if ( $padSeqName and $padSeqName !== TRUE )
      if ( $padSeqStoreUpdated ) padSeqStore ( $padSeqName );
      else                       padSeqStore ( $padSeqName );

  }

  if ( $padSeqPush )
    if ( $padSeqPush === TRUE )
      if ( $padSeqPull )
        padSeqStore ( $padSeqPull );
      else
        padSeqStore ( $padSeqName );
    else
       padSeqStore ( $padSeqPush );

?>