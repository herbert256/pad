<?php

  // pad work <app>: the queue worker - runs the application's queued jobs as they come due
  // (pad/lib/queue.php), inside the application: its _lib, configuration, .env and database.
  //
  //   pad work shop                       works the default queue until it is stopped
  //   pad work shop --queue=mail,default  several queues, the first that has a job due first
  //   pad work shop --once                what is due now, then it ends - for cron and tests
  //   pad work shop --max=100             ends after a hundred jobs - a supervisor restarts it
  //   pad work shop --sleep=3             the seconds it waits when nothing is due (1)
  //   pad work shop --tries=5             the tries of every job, over each one's own
  //   pad work shop --timeout=120         the seconds a job may take (60)
  //
  // A line per job: the time, done, retried or failed, the job, its id, how long it took,
  // and why it failed. A worker stops between two jobs on SIGTERM or SIGINT, where pcntl
  // is there - the job it runs is finished first; without pcntl a signal ends it at once,
  // and the job it ran is released after its timeout. Exit status 0, also when jobs failed
  // - they are the queue's to retry; 1 for a fault in the command.

  $workArgs  = array_slice ( $argv, 2 );
  $workWords = array_values ( array_filter ( $workArgs, fn ( $one ) => ! str_starts_with ( $one, '--' ) ) );
  $workApp   = trim ( $workWords [0] ?? '', '/' );

  foreach ( $workArgs as $workArg )
    if ( str_starts_with ( $workArg, '--' ) and ! preg_match ( '/^--(queue|once|max|sleep|tries|timeout)(=|$)/', $workArg ) )
      return cliFail ( "work: there is no option '$workArg' - pad work <app> [--queue=default] [--once] [--max=n] [--sleep=1] [--tries=3] [--timeout=60]" );

  if ( ! cliApp ( $workApp ) or count ( $workWords ) > 1 )
    return cliFail ( "there is no application named '$workApp' - pad work <app> [--queue=default] [--once] [--max=n]" );

  $workOpts = [
    'queues'  => array_values ( array_filter ( array_map ( 'trim', explode ( ',', (string) cliOption ( $workArgs, 'queue', 'default' ) ) ) ) ),
    'once'    => cliOption ( $workArgs, 'once', FALSE ) === TRUE,
    'max'     => cliOption ( $workArgs, 'max',     '0'  ),
    'sleep'   => cliOption ( $workArgs, 'sleep',   '1'  ),
    'tries'   => cliOption ( $workArgs, 'tries',   ''   ),
    'timeout' => cliOption ( $workArgs, 'timeout', '60' )
  ];

  foreach ( [ 'max', 'sleep', 'tries', 'timeout' ] as $workOpt )
    if ( ! ( $workOpt == 'tries' and $workOpts [$workOpt] === '' ) and ! ctype_digit ( (string) $workOpts [$workOpt] ) )
      return cliFail ( "work: --$workOpt is a whole number - not '" . $workOpts [$workOpt] . "'" );

  foreach ( $workOpts ['queues'] ?: [ '' ] as $workQueue )
    if ( ! preg_match ( '/^[A-Za-z0-9][A-Za-z0-9_-]{0,63}$/D', $workQueue ) or $workQueue === 'failed' )
      return cliFail ( "work: '$workQueue' is no queue - letters, digits, _ and -, and not 'failed'" );

  $workTask = function () use ( $workOpts ) {

    $stop = FALSE;

    if ( function_exists ( 'pcntl_signal' ) ) {
      pcntl_async_signals ( TRUE );
      pcntl_signal ( SIGTERM, function () use ( &$stop ) { $stop = TRUE; } );
      pcntl_signal ( SIGINT,  function () use ( &$stop ) { $stop = TRUE; } );
    }

    $max     = (int) $workOpts ['max'];
    $tries   = ( $workOpts ['tries'] === '' ) ? NULL : max ( 1, (int) $workOpts ['tries'] );
    $timeout = max ( 1, (int) $workOpts ['timeout'] );
    $count   = 0;

    while ( ! $stop ) {

      $ran = FALSE;

      foreach ( $workOpts ['queues'] as $queue ) {

        $result = padQueueWork ( $queue, 1, $timeout, $tries );

        if ( $result ['released'] )
          cliOut ( padNow ( 'Y-m-d H:i:s' ) . "  released {$result ['released']} job(s) of $queue a worker left behind" );

        foreach ( $result ['jobs'] as $job )
          cliOut ( sprintf ( '%s  %-8s %-24s %s  %s ms%s', padNow ( 'Y-m-d H:i:s' ), $job ['status'], $job ['job'],
                             $job ['id'], $job ['ms'], $job ['error'] !== '' ? '  ' . $job ['error'] : '' ) );

        if ( $result ['jobs'] ) {
          $ran = TRUE;
          $count++;
          break;
        }

      }

      if ( $max and $count >= $max )
        break;

      if ( ! $ran ) {

        if ( $workOpts ['once'] )
          break;

        sleep ( max ( 1, (int) $workOpts ['sleep'] ) );

      }

    }

    return 0;

  };

  include cliTask ( $workApp, $workTask );

  return 0;

?>
