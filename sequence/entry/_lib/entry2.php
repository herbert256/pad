<?php

  $padSeqEntryName = $padFirstParm [$pad];

  $padSeqNoNo [$padSeqEntryName] = TRUE;
  
  if ( $padSeqEntryName == $padOpt [$pad] [1] ?? '' ) 
    $padSeqEntryParm = $padOpt [$pad] [2] ?? '';
  else
    unset ( $padSeqEntryList [$padSeqEntryName] );

  $padSeqEntryExplode = padExplode ( $padSeqEntryParm, '|' ); 
  $padSeqEntryStore   = $padSeqEntryExplode [0] ?? '';

  if ( isset ( $padSeqStore [$padSeqEntryStore] ) ) {
    $padSeqSetStore   = $padSeqEntryStore;
    unset ( $padSeqEntryExplode [0] );
    $padSeqEntryParm  = implode ( '|', $padSeqEntryExplode );
  } 

?>