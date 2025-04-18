<?php

  foreach ( $padParms [$pad] as $padParmsOne )  {

    extract ( $padParmsOne );

    if ( ! $pqSeq and pqSeq ( $padPrmName ) ) {

      $pqDone [] = $padPrmName; 
      $pqSeq     = $padPrmName;
      $pqParm    = $padPrmValue;

    } elseif ( ! $pqAction and pqAction ( $padPrmName ) ) {

      $pqDone []    = $padPrmName; 
      $pqAction     = $padPrmName;
      $pqActionParm = $padPrmValue;

    } elseif ( ! $pqPull and isset ( $pqStore [$padPrmName] ) ) {

      $pqDone [] = $padPrmName;
      $pqPull    = $padPrmName;
          
    }

  }

?>