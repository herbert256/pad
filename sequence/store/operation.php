<?php

  $padSeqSeq   = $padSeqStoreAction;
  $padSeqBuild = padSeqBuild ( $padSeqSeq, 'make' );

  include '/pad/sequence/build/include.php';

  $padSeqResultActionSave = $padSeqResult;

  foreach ( $padSeqStoreNames as $padSeqStoreName ) {

    $padIdx = 0;

    while ( isset ( $padSeqResult [$padIdx] ) and isset ( $padSeqStore [$padSeqStoreName] [$padIdx] ) ) {

      $padSeqParm = $padSeqResult [$padIdx];
      $padSeqLoop = $padSeqStore [$padSeqStoreName] [$padIdx];
      $padSeqSave = $padSeqLoop;

      $padSeqResult [$padIdx] = include '/pad/sequence/build/call.php';

      if     ( $padSeqResult [$padIdx] === FALSE ) unset ( $padSeqResult [$padIdx] ) ;
      elseif ( $padSeqResult [$padIdx] === TRUE  ) $padSeqResult [$padIdx] = $padSeqSave;

      $padIdx++;

    }

    $padSeqResult = $padSeqResultActionSave;

  }

  $padSeqDone [] = 'store'. ucfirst ( padSeqSeq );
  
?>