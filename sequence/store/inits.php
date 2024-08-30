<?php

  $padSeqSeqSave   = $padSeqSeq;
  $padSeqBuildSave = $padSeqBuild;

  $padSeqStoreList = [];

  foreach ( $padOptionsMulti as $padStartOption ) {

    extract ( $padStartOption );

    if ( ! str_starts_with ( $padPrmName, 'store' ) )
      continue;

    $padSeqStoreAction = strtolower ( substr ( $padPrmName, 5, 1 ) ) . substr ( $padPrmName, 6 );

    if ( file_exists ( "/pad/sequence/types/$padSeqStoreAction" ) ) 
      include '/pad/sequence/store/initOperation.php';

    if ( file_exists ( "/pad/sequence/actions/types/$padSeqStoreAction" ) ) 
      include '/pad/sequence/store/initAction.php';

  }

  $padSeqSeq   = $padSeqSeqSave;
  $padSeqBuild = $padSeqBuildSave;

?>