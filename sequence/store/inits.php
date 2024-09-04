<?php

  $padSeqStoreList = [];

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( ! str_starts_with ( $padPrmName, 'store' ) or strlen ( $padPrmName) == 5 )
      continue;

    $padSeqStoreAction = strtolower ( substr ( $padPrmName, 5, 1 ) ) . substr ( $padPrmName, 6 );

    $padSeqStoreEntry = [
      'padSeqStoreNames'  => padExplode ( $padPrmValue ), 
      'padSeqStoreAction' => $padSeqStoreAction
    ];

    if ( file_exists ( "/pad/sequence/store/operations/$padSeqStoreAction" ) ) 
      include '/pad/sequence/store/initOperation.php';

    if ( file_exists ( "/pad/sequence/store/actions/$padSeqStoreAction" ) ) 
      include '/pad/sequence/store/initAction.php';

  }
 
?>