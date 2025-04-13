<?php

  if ( $padSeqPrefix and isset ( $padSeqStore [$padSeqPrefix] ) )
    if ( padSeqPlay ( $padSeqTag ) ) 
      if ( $padSeqFirst and file_exists ( "sequence/types/$padSeqFirst" ) ) {
        $padSeqPull    = $padSeqPrefix;
        $padSeqPlay    = $padSeqTag;
        $padSeqSeq     = $padSeqFirst;
        $padSeqParm    = $padSeqFirstParm;
        $padSeqDone [] = $padSeqFirst;
        return;
      }

  if ( $padSeqPrefix and file_exists ( "sequence/types/$padSeqPrefix" ) )
    if ( isset ( $padSeqStore [$padSeqTag] ) )
      if ( padSeqPlay ( $padSeqFirst ) ) {
        $padSeqPull    = $padSeqTag;
        $padSeqSeq     = $padSeqPrefix;
        $padSeqType    = $padSeqFirst;
        $padSeqDone [] = $padSeqFirst;
        $padSeqPlay    = $padSeqFirst;
        return;
      }

?>