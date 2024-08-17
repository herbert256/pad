<?php


  function padInfoSet ( ) {

    if ( ! isset ( $GLOBALS ['padInfoTrack'] ) ) $GLOBALS ['padInfoTrack'] = FALSE;
    if ( ! isset ( $GLOBALS ['padInfoStats'] ) ) $GLOBALS ['padInfoStats'] = FALSE;
    if ( ! isset ( $GLOBALS ['padInfoXapp']  ) ) $GLOBALS ['padInfoXapp']  = FALSE;
    if ( ! isset ( $GLOBALS ['padInfoTrace'] ) ) $GLOBALS ['padInfoTrace'] = FALSE;
    if ( ! isset ( $GLOBALS ['padInfoXml']   ) ) $GLOBALS ['padInfoXml']   = FALSE;
    if ( ! isset ( $GLOBALS ['padInfoXref']  ) ) $GLOBALS ['padInfoXref']  = FALSE;

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