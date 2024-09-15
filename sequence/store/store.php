<?php

  if ( strlen ( $padPrmName ) == 5 ) {
    $padSeqStore [$padPrmValue] = array_values ( $padSeqResult );
    $padSeqNames [] = $padPrmValue;
    return;
  }

  $padSeqResultSave  = $padSeqResult;

  $padSeqStoreAction = strtolower ( substr ( $padPrmName, 5, 1 ) ) . substr ( $padPrmName, 6 );
  $padSeqStoreNames  = padExplode ( $padPrmValue, '|' );
  $padSeqStoreName   = $padSeqStoreNames [0];

  if ( file_exists ( "/pad/sequence/store/operations/$padSeqStoreAction" ) ) 
      include '/pad/sequence/store/operation.php';

  if ( file_exists ( "/pad/sequence/store/actions/$padSeqStoreAction" ) ) 
      include '/pad/sequence/store/action.php';

  $padSeqResult = $padSeqResultSave;
 
?>