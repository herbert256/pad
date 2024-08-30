<?php

  if ( ! $padSeqStoreName )
    $padSeqStoreName = $padSeqName;

  foreach ( $padSeqStoreList as $padSeqStoreAction => $padSeqStoreValue ) {
  
    $padSeqActionName  = strtolower ( substr ( $padSeqStoreAction, 5, 1 ) ) . substr ( $padSeqStoreAction, 6 );
    $padSeqActionList  = [$padSeqStoreName];
    $padSeqActionValue = ( $padSeqStoreValue === TRUE ) ? $padSeqStoreName: $padSeqStoreValue;

                    include "/pad/sequence/store/types/$padSeqStoreAction.php";
    $padSeqResult = include "/pad/sequence/actions/types/$padSeqActionName.php";

  }
  
  $padSeqStore [$padSeqStoreName] = $padSeqResult;

?>