<?php

  if ( $padPrmName == 'store' )
    return;
  
  $padSeqResultSave  = $padSeqResult;
  $padSeqStoreAction = strtolower ( substr ( $padPrmName, 5, 1 ) ) . substr ( $padPrmName, 6 );
  $padSeqPushNames  = padExplode ( $padPrmValue, '|' );
  $padSeqPushName   = $padSeqPushNames [0];

  if ( file_exists ( "sequence/store/plays/$padSeqStoreAction" ) ) 
      include 'sequence/store/play.php';

  if ( file_exists ( "sequence/store/actions/$padSeqStoreAction" ) ) 
      include 'sequence/store/action.php';

  $padSeqResult = $padSeqResultSave;

?>