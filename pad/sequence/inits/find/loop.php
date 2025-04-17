<?php

  foreach ( $padParms [$pad] as $padParmsOne )  {

    extract ( $padParmsOne );

    if ( ! $padSeqAction and file_exists ( "sequence/actions/types/$padPrmName.php" ) ) {

      $padSeqDone []    = $padPrmName; 
      $padSeqAction     = $padPrmName;
      $padSeqActionParm = $padPrmValue;

      return; 

    } elseif ( ! $padSeqSeq and file_exists ( "sequence/types/$padPrmName" ) ) {

      $padSeqDone [] = $padPrmName; 
      $padSeqSeq     = $padPrmName;
      $padSeqParm    = $padPrmValue;

      return; 

    }

  }

?>