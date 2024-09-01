<?php

  $padSeqSeq   = $padSeqStoreAction;
  $padSeqBuild = padSeqBuild ( $padSeqSeq, 'make' );

  include '/pad/sequence/build/include.php';

  if ( ! file_exists ( "/pad/sequence/types/$padSeqSeq/flags/parm" ) ) 

    foreach ( $padSeqResult as $padIdx => $padSeqLoop )
      include '/pad/sequence/store/operation.php';

   else {

    $padIdx = 0;

    while ( isset ( $padSeqResult [$padIdx] ) and isset ( $padSeqStore [$padSeqStoreName] [$padIdx] ) ) {

      $padSeqParm = $padSeqResult [$padIdx];
      $padSeqLoop = $padSeqStore [$padSeqStoreName] [$padIdx];

      include '/pad/sequence/store/operation.php';

      $padIdx++;

    }

  }
  
?>