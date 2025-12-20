<?php

  foreach ( $padParms [$pad] as $padParmsOne )  {

    extract ( $padParmsOne );

    if ( ! $pqPull and isset ( $pqStore [$padPrmName] ) )
      $pqPull = $padPrmName;

  }

  foreach ( $padParms [$pad] as $padParmsOne )  {

    extract ( $padParmsOne );

    if ( ! $pqPull and ! $pqSeq and pqSeq ( $padPrmName ) ) {

      $pqDone [] = $padPrmName;
      $pqSeq     = $padPrmName;
      $pqParm    = $padPrmValue;

    }

  }

?>
