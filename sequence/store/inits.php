<?php

  $padSeqStoreList = [];

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( ! str_starts_with ( $padPrmName, 'store' ) or strlen ( $padPrmName) == 5 )
      continue;

    $padSeqStoreAction = strtolower ( substr ( $padPrmName, 5, 1 ) ) . substr ( $padPrmName, 6 );

    $padSeqStoreEntry = [
      'padSeqStoreName'   => $padPrmValue,
      'padSeqStoreAction' => $padSeqStoreAction
    ];

    if ( file_exists ( "/pad/sequence/types/$padSeqStoreAction" ) ) 
      include '/pad/sequence/store/initOperation.php';

    if ( file_exists ( "/pad/sequence/actions/types/$padSeqStoreAction.php" ) ) 
      include '/pad/sequence/store/initAction.php';

  }
 
?>