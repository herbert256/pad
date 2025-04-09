<?php

  $padSeqSeq   = $padSeqStoreAction;
  $padSeqBuild = padSeqBuild ( $padSeqSeq, 'make' );

  include 'sequence/build/include.php';

  $padIdx = 0;

  while ( isset ( $padSeqResult [$padIdx] ) and isset ( $padSeqStore [$padSeqPushName] [$padIdx] ) ) {

    $padSeqParm = $padSeqResult [$padIdx];
    $padSeqLoop = $padSeqStore [$padSeqPushName] [$padIdx];
    $padSeqSave = $padSeqLoop;

    $padSeqResult [$padIdx] = include 'sequence/build/call.php';

    if     ( $padSeqResult [$padIdx] === FALSE ) unset ( $padSeqResult [$padIdx] ) ;
    elseif ( $padSeqResult [$padIdx] === TRUE  ) $padSeqResult [$padIdx] = $padSeqSave;

    $padIdx++;

  }

  $padSeqStore [$padSeqPushName] = array_values ( $padSeqResult );

  $padSeqNames [] = $padSeqPushName;

  $padSeqDone [] = 'store'. ucfirst ( $padSeqSeq );
  
  $padSeqInfo ['store/plays'] [] = $padSeqSeq;

?>