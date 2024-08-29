<?php

  foreach ( $padSeqStoreList as $padSeqActionName => $padSeqActionValue ) {

    if ( str_contains ( $padSeqActionValue, '|' ))
      $padSeqActionList = padExplode ( "$padSeqActionValue|$padSeqStoreName", '|' );
    else
      $padSeqActionList = [$padSeqStoreName];

    if ( ! $padSeqActionValue )
      $padSeqActionValue = $padSeqStoreName;

    $padSeqStoreTemp                = $padSeqResult;
    $padSeqResult                   = $padSeqStore [$padSeqStoreName];
    $padSeqStore [$padSeqStoreName] = $padSeqStoreTemp;

                  include "/pad/sequence/store/types/$padSeqActionName.php";
    $padSeqResult = include "/pad/sequence/actions/types/$padSeqActionName.php";
   

  }
  
?>