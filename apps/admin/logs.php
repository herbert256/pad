<?php

  // The application logs padLog writes - DATA/logs/<app>/<date>.log, one line per entry:
  // time, level, message and its context as JSON. The list of the files, and one of them
  // read from its end, filtered by level and by text.

  global $adminLogTail;

  $title = 'Logs';
  $app   = adminAppAsked ();
  $date  = adminGet ( 'date' );
  $level = strtoupper ( adminGet ( 'level' ) );
  $grep  = trim ( adminGet ( 'q' ) );

  if ( ! preg_match ( '/^\d{4}-\d{2}-\d{2}$/D', $date ) )
    $date = '';

  $levels = [ 'DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY' ];

  if ( ! in_array ( $level, $levels, TRUE ) )
    $level = '';

  if ( adminPost () and $app !== '' and $date !== '' and adminField ( 'action' ) == 'delete' ) {
    adminRemove ( DATA . "logs/$app/$date.log" );
    adminDone ( "The log of $app for $date is removed.", 'logs', [ 'app' => $app ] );
  }

  // Every log file, newest first.

  $fileRows = [];

  foreach ( adminApps () as $name => $one )
    if ( $app === '' or $name === $app )
      foreach ( glob ( DATA . "logs/$name/*.log" ) ?: [] as $file )
        $fileRows [] = [ 'app' => $name, 'date' => basename ( $file, '.log' ), 'size' => adminBytes ( filesize ( $file ) ),
                         'time' => filemtime ( $file ), 'ago' => adminAgo ( filemtime ( $file ) ),
                         'current' => ( $name === $app and basename ( $file, '.log' ) === $date ) ? 1 : 0 ];

  usort ( $fileRows, fn ( $a, $b ) => [ $b ['date'], $a ['app'] ] <=> [ $a ['date'], $b ['app'] ] );

  $fileCount = count ( $fileRows );

  // The one asked for: its last lines, the newest on top.

  $lineRows = [];
  $viewing  = 0;

  if ( $app !== '' and $date !== '' and is_file ( DATA . "logs/$app/$date.log" ) ) {

    $viewing = 1;
    $title   = "Log of $app, $date";

    foreach ( array_reverse ( adminTail ( DATA . "logs/$app/$date.log", $adminLogTail ) ) as $line ) {

      if ( ! preg_match ( '/^(\d{4}-\d{2}-\d{2} )?(\d{2}:\d{2}:\d{2}) ([A-Z]+) (.*)$/s', $line, $match ) )
        $match = [ '', '', '', '', $line ];

      if ( $level !== '' and $match [3] !== $level )
        continue;

      if ( $grep !== '' and stripos ( $line, $grep ) === FALSE )
        continue;

      $lineRows [] = [ 'at' => $match [2], 'level' => $match [3], 'text' => $match [4],
                       'tone' => match ( $match [3] ) { 'DEBUG', 'INFO' => '', 'NOTICE' => 'info', 'WARNING' => 'warn', default => 'bad' } ];

    }

  }

  $lineCount = count ( $lineRows );

  $levelRows = array_map ( fn ( $one ) => [ 'name' => $one, 'selected' => $one === $level ? 1 : 0 ], $levels );

  // PHP's own error log, when it is a file this process can read.

  $phpLog     = (string) ini_get ( 'error_log' );
  $phpLogRows = ( $phpLog !== '' and is_file ( $phpLog ) and is_readable ( $phpLog ) ) ? array_reverse ( adminTail ( $phpLog, 50 ) ) : [];
  $phpLogText = implode ( "\n", $phpLogRows );
  $hasPhpLog  = count ( $phpLogRows ) ? 1 : 0;

?>
