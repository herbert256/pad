<?php

  // The job queues of every application (pad/lib/queue.php): per queue the jobs due now,
  // delayed and running, and the jobs that failed for good with why. The queue state is
  // read from DATA/queue/<app>/ directly; what changes it - a retry, a flush, a worker
  // run - goes through the pad command for that application, as the command line would.

  $title = 'Queues';
  $app   = adminAppAsked ();

  if ( adminPost () ) {

    $action = adminField ( 'action' );
    $id     = adminField ( 'id' );
    $queue  = adminField ( 'queue' );

    if ( $app === '' )
      adminDone ( 'Choose an application.', 'queues', [], 'error' );

    $args = match ( TRUE ) {
      $action == 'retry' and ( $id === 'all' or padQueueIdValid ( $id ) ) => [ 'queue', $app, "--retry=$id" ],
      $action == 'flush-failed'                                             => [ 'queue', $app, '--flush' ],
      $action == 'flush' and padQueueNameValid ( $queue )                   => [ 'queue', $app, "--flush=$queue" ],
      $action == 'work'                                                     => [ 'work', $app, '--once' ],
      default                                                               => NULL
    };

    if ( $args === NULL )
      adminDone ( 'That is no action of this page.', 'queues', [ 'app' => $app ], 'error' );

    [ $code, $out, $ms ] = adminRun ( $args );

    adminHistoryAdd ( 'pad ' . implode ( ' ', $args ), $code, $ms );

    adminDone ( "pad " . implode ( ' ', $args ) . ': ' . ( $out === '' ? 'done' : $out ), 'queues', [ 'app' => $app ], $code ? 'error' : 'ok' );

  }

  $queueApps = [];

  foreach ( adminApps () as $name => $one ) {

    if ( $app !== '' and $name !== $app )
      continue;

    $dir = DATA . "queue/$name/";

    if ( ! is_dir ( $dir ) and $app === '' )
      continue;

    $queues = [];
    $now    = time ();

    foreach ( glob ( $dir . '*', GLOB_ONLYDIR ) ?: [] as $queueDir ) {

      $queueName = basename ( $queueDir );

      if ( ! padQueueNameValid ( $queueName ) )
        continue;

      $due = $delayed = 0;

      foreach ( glob ( "$queueDir/*.job" ) ?: [] as $file )
        if ( (int) substr ( basename ( $file ), 0, 10 ) <= $now ) $due++; else $delayed++;

      $queues [] = [ 'queue' => $queueName, 'due' => $due, 'delayed' => $delayed,
                     'running' => count ( glob ( "$queueDir/*.run" ) ?: [] ) ];

    }

    $failed = [];

    foreach ( glob ( $dir . '_failed/*.json' ) ?: [] as $file ) {

      $record = padQueueRead ( $file );

      if ( $record )
        $failed [] = [ 'id' => $record ['id'], 'queue' => $record ['queue'] ?? '', 'job' => $record ['job'],
                       'attempts' => $record ['attempts'] ?? 0, 'when' => adminWhen ( $record ['failed'] ?? 0 ),
                       'why' => (string) ( $record ['error'] ?? '' ),
                       'payload' => json_encode ( $record ['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ];

    }

    $queueApps [] = [ 'name' => $name, 'queues' => $queues, 'queueCount' => count ( $queues ),
                      'failedRows' => $failed, 'failedCount' => count ( $failed ),
                      'hasJobs' => is_dir ( $one ['dir'] . '_jobs' ) ? 1 : 0 ];

  }

  $queueAppCount = count ( $queueApps );

  $appRows = [];

  foreach ( adminApps () as $name => $one )
    if ( $name !== '_common' )
      $appRows [] = [ 'name' => $name, 'selected' => $name === $app ? 1 : 0 ];

?>
