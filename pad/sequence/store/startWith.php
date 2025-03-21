<?php

  if ( $padPrmName == 'store' )
    return;
  
  $padSeqResultSave  = $padSeqResult;
  $padSeqStoreAction = strtolower ( substr ( $padPrmName, 5, 1 ) ) . substr ( $padPrmName, 6 );
  $padSeqStoreNames  = padExplode ( $padPrmValue, '|' );
  $padSeqStoreName   = $padSeqStoreNames [0];

  if ( file_exists ( "sequence/store/plays/$padSeqStoreAction" ) ) 
      include 'sequence/store/play.php';

  if ( file_exists ( "sequence/store/actions/$padSeqStoreAction" ) ) 
      include 'sequence/store/action.php';

  $padSeqResult = $padSeqResultSave;

?>