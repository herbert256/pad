<?php

    $padSeqNoNo [$padSeqEntryName] = TRUE;

    if ( $padSeqEntryType == 'type' )
      $padSeqEntryParm = $padOpt [$pad] [1]; 
    else
      $padSeqEntryParm = $padPrm [$pad] [$padSeqEntryName]; 

    $padSeqEntryList = $padPrm [$pad];
    unset ( $padSeqEntryList [$padSeqEntryName] );

    $padSeqEntryExplode = padExplode ( $padSeqEntryParm, '|' ); 
    $padSeqEntryStore   = $padSeqEntryExplode [0] ?? '';

    if ( isset ( $padSeqStore [$padSeqEntryStore] ) ) {
      $padSeqSetStore   = $padSeqEntryStore;
      unset ( $padSeqEntryExplode [0] );
      $padSeqEntryParm  = implode ( '|', $padSeqEntryExplode );
    } 

?>