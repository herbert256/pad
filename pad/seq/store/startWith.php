<?php

  $padSeqResultSave  = $padSeqResult;
  $padSeqStoreAction = strtolower ( substr ( $padPrmName, 5, 1 ) ) . substr ( $padPrmName, 6 );
  $padSeqStoreNames  = padExplode ( $padPrmValue, '|' );
  $padSeqStoreName   = $padSeqStoreNames [0];

  if ( file_exists ( "seq/store/plays/$padSeqStoreAction" ) ) 
      include 'seq/store/play.php';

  if ( file_exists ( "seq/store/actions/$padSeqStoreAction" ) ) 
      include 'seq/store/action.php';

  $padSeqResult = $padSeqResultSave;

?>