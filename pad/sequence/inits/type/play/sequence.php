<?php

  if ( $padSeqPrefix )
    if ( padSeqPlay ( $padSeqTag ) and file_exists ( "sequence/types/$padSeqPrefix" ) ) {
      $padSeqPlay = $padSeqTag;
      $padSeqSeq  = $padSeqPrefix;      
    } elseif ( padSeqPlay ( $padSeqPrefix ) and file_exists ( "sequence/types/$padSeqTag" ) ) {
      $padSeqPlay = $padSeqPrefix;
      $padSeqSeq  = $padSeqTag;
    }

  if ( $padSeqPlay and $padSeqSeq )
    if ( $padSeqPullName ) {
      $padSeqPull     = $padSeqPullName;
      $padSeqPullName = '';    
    } elseif ( $padSeqFirst and isset ( $padSeqStore [$padSeqFirst] ) ) {
      $padSeqPull    = $padSeqFirst;
      $padSeqDone [] = $padSeqFirst;
    } elseif ( $padLastPush )
      $padSeqPull = $padLastPush;

?>