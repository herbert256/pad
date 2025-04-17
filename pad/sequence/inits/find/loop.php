<?php

  foreach ( $padParms [$pad] as $padParmsOne )  {

    extract ( $padParmsOne );

    if ( ! $padSeqSeq and file_exists ( "sequence/types/$padPrmName" ) ) {

      $padSeqDone [] = $padPrmName; 
      $padSeqSeq     = $padPrmName;
      $padSeqParm    = $padPrmValue;

    } elseif ( ! $padSeqAction and file_exists ( "sequence/actions/types/$padPrmName.php" ) ) {

      $padSeqDone []    = $padPrmName; 
      $padSeqAction     = $padPrmName;
      $padSeqActionParm = $padPrmValue;

    } elseif ( ! $padSeqPull and isset ( $padSeqStore [$padPrmName] ) ) {

      $padSeqDone [] = $padPrmName;
      $padSeqPull    = $padPrmName;
          
    }

  }

?>