<?php

  $padSeqSeq   = $padSeqStoreAction;
  $padSeqBuild = padSeqBuild ( $padSeqSeq, 'make' );

  include PAD . 'seq/build/include.php';

  $padIdx = 0;

  while ( isset ( $padSeqResult [$padIdx] ) and isset ( $padSeqStore [$padSeqStoreName] [$padIdx] ) ) {

    $padSeqParm = $padSeqResult [$padIdx];
    $padSeqLoop = $padSeqStore [$padSeqStoreName] [$padIdx];
    $padSeqSave = $padSeqLoop;

    $padSeqResult [$padIdx] = include PAD . 'seq/build/call.php';

    if     ( $padSeqResult [$padIdx] === FALSE ) unset ( $padSeqResult [$padIdx] ) ;
    elseif ( $padSeqResult [$padIdx] === TRUE  ) $padSeqResult [$padIdx] = $padSeqSave;

    $padIdx++;

  }

  $padSeqStore [$padSeqStoreName] = array_values ( $padSeqResult );

  $padSeqNames [] = $padSeqStoreName;

  $padSeqDone [] = 'store'. ucfirst ( $padSeqSeq );
  
  $padSeqInfo ['store/operations'] [] = $padSeqSeq;

?>