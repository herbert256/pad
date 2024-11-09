<?php

  $padSeqNoNo [$padSeqEntryName] = TRUE;

  if ( $padSeqEntryParm === TRUE )
    $padSeqEntryParm = '';

  unset ( $padSeqEntryList [$padSeqEntryName] );

  $padSeqEntryExplode = padExplode ( $padSeqEntryParm, '|' ); 
  $padSeqEntryStore   = $padSeqEntryExplode [0] ?? '';

  if ( isset ( $padSeqStore [$padSeqEntryStore] ) ) {
    $padSeqSetStore   = $padSeqEntryStore;
    unset ( $padSeqEntryExplode [0] );
    $padSeqEntryParm  = implode ( '|', $padSeqEntryExplode );
  } 

?>