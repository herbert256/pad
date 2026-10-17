<?php

  // Small helpers shared by all $padInfo report modes, loaded once by info/start/config.php
  // and info/start/tag.php - read only when a request or a {trace} reports.
  //
  // padInfoSet       defaults every switch to FALSE so later code can test them unguarded
  // padInfoBackup    snapshots all padInfo* globals into $padInfoBackup[$padInfoCnt]
  // padInfoRestore   drops the current padInfo* globals and puts that snapshot back
  //
  // Backup/restore let a tag run under its own info settings (info/start/tag.php) without
  // leaking them; $padInfoCnt and $padInfoTraceId are excluded as they must survive.
  //
  // padInfoPadOccur  labels the current position as "level" or "level/occurrence", leaving the
  //                  inits (0) and exits (99999) sentinel occurrences off
  // padInfoGet       reads a report file, returning '' instead of failing when it is absent
  // padInfoDelete    removes a report directory and everything below it
  //
  // Only padInfoGet currently has callers; the other two are kept for report tooling.

  function padInfoPadOccur () {

    global $pad, $padOccur;

    $return = $pad;

    $occur = $padOccur [$pad] ?? 0;

    if ( $occur != 0 and $occur != 99999 )
      $return .= "/$occur";

    return $return;

  }

  function padInfoGet ( $file ) {

    if ( ! file_exists ($file) )
      return '';

    return padFileGet ($file);

  }

  function padInfoDelete ( $dir ) {

    if ( ! file_exists ( $dir ) )
      return;

    $loop = opendir ( $dir );

    while ( ( $file = readdir ( $loop ) ) !== FALSE )

      if ( $file != '.' and $file != '..' )
        if ( is_dir ( "$dir/$file" ) )
          padInfoDelete ( "$dir/$file" );
        else
          unlink ( "$dir/$file" ) ;

    closedir ( $loop );

    rmdir ( $dir );

  }

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
      if ( str_starts_with($k, 'padInfo') and $k != 'padInfoBackup' )
        if ( $k != 'padInfoCnt' and $k != 'padInfoTraceId' )
          $GLOBALS ['padInfoBackup'] [$padInfoCnt] [$k] = $v;

  }

  function padInfoRestore ( ) {

    global $padInfoCnt;

    foreach ( $GLOBALS as $k => $v )
      if ( str_starts_with($k, 'padInfo') and $k != 'padInfoBackup' )
        if ( $k != 'padInfoCnt' and $k != 'padInfoTraceId' )
          unset ( $GLOBALS [$k] );

    foreach ( $GLOBALS ['padInfoBackup'] [$padInfoCnt] as $k => $v )
      $GLOBALS [$k] = $v;

  }

?>
