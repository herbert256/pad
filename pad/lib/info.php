<?php

  function padInfoSet ( ) {

    global $padInfoStats, $padInfoTrace, $padInfoTrack, $padInfoXml, $padInfoXref;

    if ( ! isset ( $padInfoTrack ) ) $padInfoTrack = FALSE;
    if ( ! isset ( $padInfoStats ) ) $padInfoStats = FALSE;
    if ( ! isset ( $padInfoXref  ) ) $padInfoXref  = FALSE;
    if ( ! isset ( $padInfoTrace ) ) $padInfoTrace = FALSE;
    if ( ! isset ( $padInfoXml   ) ) $padInfoXml   = FALSE;

  }

  function padInfoBackup ( ) {

    global $padInfoCnt;

    foreach ( $GLOBALS as $k => $v )
      if ( str_starts_with($k, 'padInfo') and $k <> 'padInfoBackup' )
        if ( $k <> 'padInfoCnt' and $k <> 'padInfoTraceId' )
          $GLOBALS ['padInfoBackup'] [$padInfoCnt] [$k] = $v;

  }

  function padInfoRestore ( ) {

    global $padInfoCnt;

    foreach ( $GLOBALS as $k => $v )
      if ( str_starts_with($k, 'padInfo') and $k <> 'padInfoBackup' )
        if ( $k <> 'padInfoCnt' and $k <> 'padInfoTraceId' )
          unset ( $GLOBALS [$k] );

    foreach ( $GLOBALS ['padInfoBackup'] [$padInfoCnt] as $k => $v )
      $GLOBALS [$k] = $v;

  }

?>
