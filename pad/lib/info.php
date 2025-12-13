<?php


  /**
   * Initializes info mode global variables.
   *
   * Sets default FALSE values for all info mode flags if not
   * already set.
   *
   * @return void
   */
  function padInfoSet ( ) {

    if ( ! isset ( $GLOBALS ['padInfoTrack'] ) ) $GLOBALS ['padInfoTrack'] = FALSE;
    if ( ! isset ( $GLOBALS ['padInfoStats'] ) ) $GLOBALS ['padInfoStats'] = FALSE;
    if ( ! isset ( $GLOBALS ['padInfoXref']  ) ) $GLOBALS ['padInfoXref']  = FALSE;
    if ( ! isset ( $GLOBALS ['padInfoTrace'] ) ) $GLOBALS ['padInfoTrace'] = FALSE;
    if ( ! isset ( $GLOBALS ['padInfoXml']   ) ) $GLOBALS ['padInfoXml']   = FALSE;

  }


  /**
   * Backs up current info mode settings.
   *
   * Saves all padInfo* globals (except counters) to backup array
   * indexed by current info count.
   *
   * @return void
   *
   * @global int $padInfoCnt Current info nesting level.
   */
  function padInfoBackup ( ) {

    global $padInfoCnt;

    foreach ( $GLOBALS as $k => $v )
      if ( str_starts_with($k, 'padInfo') and $k <> 'padInfoBackup' )
        if ( $k <> 'padInfoCnt' and $k <> 'padInfoTraceId' )
          $GLOBALS ['padInfoBackup'] [$padInfoCnt] [$k] = $v;

  }


  /**
   * Restores previously backed up info mode settings.
   *
   * Clears current padInfo* globals and restores from backup
   * at current info count level.
   *
   * @return void
   *
   * @global int $padInfoCnt Current info nesting level.
   */
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