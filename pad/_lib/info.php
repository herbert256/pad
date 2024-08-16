<?php


  function padInfoSet ( ) {

    if ( ! isset ( $GLOBALS ['padInfTrack'] ) ) $GLOBALS ['padInfTrack'] = FALSE;
    if ( ! isset ( $GLOBALS ['padInfStats'] ) ) $GLOBALS ['padInfStats'] = FALSE;
    if ( ! isset ( $GLOBALS ['padInfXapp']  ) ) $GLOBALS ['padInfXapp']  = FALSE;
    if ( ! isset ( $GLOBALS ['padInfTrace'] ) ) $GLOBALS ['padInfTrace'] = FALSE;
    if ( ! isset ( $GLOBALS ['padInfXml']   ) ) $GLOBALS ['padInfXml']   = FALSE;
    if ( ! isset ( $GLOBALS ['padInfXref']  ) ) $GLOBALS ['padInfXref']  = FALSE;

  }


  function padInfoBackup ( ) {

    padInfoSet ();    

    global $padInfCnt; 

    foreach ( $GLOBALS as $k => $v )
      if ( str_starts_with($k, 'padInf') and $k <> 'padInfCnt' and $k <> 'padInfBackup' )
        $GLOBALS ['padInfBackup'] [$padInfCnt] [$k] = $v;

  }


  function padInfoRestore ( ) {

    global $padInfCnt; 

    foreach ( $GLOBALS ['padInfBackup'] [$padInfCnt] as $k => $v )
      $GLOBALS [$k] = $v;

  }


?>