<?php

  foreach ( $padParms [$pad] as $padParmsOne )  {

    extract ( $padParmsOne );

    if ( ! $pqSeq and file_exists ( "sequence/types/$padPrmName" ) ) {

      $pqDone [] = $padPrmName; 
      $pqSeq     = $padPrmName;
      $pqParm    = $padPrmValue;

    } elseif ( ! $pqAction and file_exists ( "sequence/actions/types/$padPrmName.php" ) ) {

      $pqDone []    = $padPrmName; 
      $pqAction     = $padPrmName;
      $pqActionParm = $padPrmValue;

    } elseif ( ! $pqPull and isset ( $pqStore [$padPrmName] ) ) {

      $pqDone [] = $padPrmName;
      $pqPull    = $padPrmName;
          
    }

  }

?>