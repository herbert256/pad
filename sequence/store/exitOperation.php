<?php

  if ( ! count ( $padSeqResult )                   ) return;
  if ( ! count ( $padSeqStore [$padSeqStoreName] ) ) return;

  $padSeqResult   = array_values ( $padSeqResult );
  $padStoreStore  = array_values ( $padSeqStore [$padSeqStoreName] );
  $padStoreIndex  = 0;

  while ( isset ( $padSeqResult [$padStoreIndex] ) and isset ( $padStoreStore [$padStoreIndex] ) ) {

    $padSeqName = $padSeqSeq;
    $padSeqParm = $padSeqResult  [$padStoreIndex];
    $padSeqLoop = $padStoreStore [$padStoreIndex];
    $padSeqSave = $padSeqLoop;

    $GLOBALS [ padSeqName ($padSeqName) ] = $padSeqParm;

    $padSeqResult [$padStoreIndex] = include '/pad/sequence/build/call.php';

    if     ( $padSeqResult [$padStoreIndex] === FALSE ) unset ( $padSeqResult [$padStoreIndex] ) ;
    elseif ( $padSeqResult [$padStoreIndex] === TRUE  ) $padSeqResult [$padStoreIndex] = $padSeqSave;
    
    $padStoreIndex++;

  }
  
?>