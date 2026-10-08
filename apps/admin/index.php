<?php

  // The dashboard: the installation at a glance - its applications, what is waiting or
  // broken under DATA/, the disk, PHP and the checkout.

  $title = 'Dashboard';

  $apps      = adminApps ();
  $appCount  = count ( $apps ) - ( isset ( $apps ['_common'] ) ? 1 : 0 );
  $down      = padMaintenanceList ();
  $downCount = count ( $down );

  $queued = $failed = 0;

  foreach ( array_keys ( $apps ) as $one ) {
    $queued += adminQueueCount ( $one );
    $failed += count ( glob ( DATA . "queue/$one/_failed/*.json" ) ?: [] );
  }

  $dumpCount = count ( glob ( DATA . 'dumps/*/*', GLOB_ONLYDIR ) ?: [] )
             + count ( glob ( DATA . 'dumps/*/*/*', GLOB_ONLYDIR ) ?: [] );
  $mailCount = count ( glob ( DATA . 'mail/*/*.eml' ) ?: [] ) + count ( glob ( DATA . 'mail/*/*/*.eml' ) ?: [] );

  $logToday = 0;

  foreach ( glob ( DATA . 'logs/*/' . date ( 'Y-m-d' ) . '.log' ) ?: [] as $file )
    $logToday += substr_count ( (string) file_get_contents ( $file ), "\n" );

  // The disk DATA/ lives on.

  $diskFree  = (float) @disk_free_space ( DATA );
  $diskTotal = (float) @disk_total_space ( DATA );
  $diskUsed  = $diskTotal > 0 ? round ( ( $diskTotal - $diskFree ) / $diskTotal * 100 ) : 0;
  $diskText  = adminBytes ( $diskFree ) . ' free of ' . adminBytes ( $diskTotal );

  // The tiles: a number, what it counts, where to look, and whether it wants attention.

  $tiles = [
    [ 'value' => $appCount,  'label' => 'applications',         'page' => 'apps',        'warn' => 0 ],
    [ 'value' => $downCount, 'label' => 'down for maintenance', 'page' => 'maintenance', 'warn' => $downCount ? 1 : 0 ],
    [ 'value' => $queued,    'label' => 'jobs queued',          'page' => 'queues',      'warn' => 0 ],
    [ 'value' => $failed,    'label' => 'jobs failed',          'page' => 'queues',      'warn' => $failed ? 1 : 0 ],
    [ 'value' => $dumpCount, 'label' => 'error dumps',          'page' => 'dumps',       'warn' => $dumpCount ? 1 : 0 ],
    [ 'value' => $logToday,  'label' => 'log lines today',      'page' => 'logs',        'warn' => 0 ],
    [ 'value' => $mailCount, 'label' => 'mails in the outbox',  'page' => 'mail',        'warn' => 0 ]
  ];

  // The system.

  $git = adminGit ( [ 'log', '-1', '--format=%h|%s|%an|%ct' ] );
  $branch = adminGit ( [ 'rev-parse', '--abbrev-ref', 'HEAD' ] );

  [ $gitHash, $gitSubject, $gitAuthor, $gitTime ] = array_pad ( $git [0] === 0 ? explode ( '|', $git [1], 4 ) : [], 4, '' );

  $facts = [
    [ 'label' => 'PAD home',       'value' => rtrim ( adminHome (), '/' ) ],
    [ 'label' => 'Server',         'value' => php_uname ( 'n' ) . ' - ' . php_uname ( 's' ) . ' ' . php_uname ( 'r' ) ],
    [ 'label' => 'Web server',     'value' => (string) ( $_SERVER ['SERVER_SOFTWARE'] ?? PHP_SAPI ) ],
    [ 'label' => 'PHP',            'value' => PHP_VERSION . ' (' . PHP_SAPI . ')' ],
    [ 'label' => 'Time',           'value' => date ( 'Y-m-d H:i:s' ) . ' ' . date_default_timezone_get () ],
    [ 'label' => 'Memory limit',   'value' => (string) ini_get ( 'memory_limit' ) ],
    [ 'label' => 'Git',            'value' => $gitHash === '' ? 'no git checkout' : trim ( $branch [1] ) . " at $gitHash - $gitSubject" ],
    [ 'label' => 'Last commit',    'value' => $gitHash === '' ? '' : "$gitAuthor, " . adminAgo ( $gitTime ) ]
  ];

  // What changed last: the newest source files of every application.

  $recent = [];

  foreach ( $apps as $name => $one )
    foreach ( adminFilesIn ( $one ['dir'] ) as $file )
      if ( preg_match ( '/\.(pad|php|html|js|css|json|md)$/', $file ) )
        $recent [] = [ 'app' => $name, 'file' => substr ( $file, strlen ( $one ['dir'] ) ), 'time' => (int) @filemtime ( $file ) ];

  usort ( $recent, fn ( $a, $b ) => $b ['time'] <=> $a ['time'] );

  $recent = array_slice ( $recent, 0, 10 );

  foreach ( $recent as &$one )
    $one ['ago'] = adminAgo ( $one ['time'] );

  unset ( $one );

  // DATA/ by directory, the biggest first.

  $dataSizes = [];

  foreach ( glob ( DATA . '*', GLOB_ONLYDIR ) ?: [] as $dir ) {
    [ $bytes ] = adminDirSize ( $dir );
    $dataSizes [] = [ 'dir' => basename ( $dir ), 'mb' => round ( $bytes / 1048576, 2 ) ];
  }

  usort ( $dataSizes, fn ( $a, $b ) => $b ['mb'] <=> $a ['mb'] );

  $dataSizes = array_slice ( $dataSizes, 0, 8 );
  $dataChart = count ( $dataSizes ) ? 1 : 0;

  $downList = [];

  foreach ( $down as $name => $one )
    $downList [] = [ 'app' => $name, 'since' => adminAgo ( $one ['time'] ), 'message' => $one ['message'] ];

?>
