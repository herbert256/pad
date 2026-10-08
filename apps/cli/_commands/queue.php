<?php

  // pad queue <app>: what is on the application's queues (pad/lib/queue.php), and the jobs
  // that failed for good.
  //
  //   pad queue shop                   every queue: due, delayed and running jobs; the failed
  //   pad queue shop --failed          the failed jobs: id, queue, job, tries, when, why
  //   pad queue shop --retry=<id>      a failed job back on its queue, its tries afresh
  //   pad queue shop --retry=all       every failed job back
  //   pad queue shop --flush           the failed jobs removed
  //   pad queue shop --flush=<queue>   the waiting jobs of a queue removed

  $queueArgs  = array_slice ( $argv, 2 );
  $queueWords = array_values ( array_filter ( $queueArgs, fn ( $one ) => ! str_starts_with ( $one, '--' ) ) );
  $queueApp   = trim ( $queueWords [0] ?? '', '/' );

  foreach ( $queueArgs as $queueArg )
    if ( str_starts_with ( $queueArg, '--' ) and ! preg_match ( '/^--(failed$|retry=|flush(=|$))/', $queueArg ) )
      return cliFail ( "queue: there is no option '$queueArg' - pad queue <app> [--failed] [--retry=<id|all>] [--flush[=queue]]" );

  if ( ! cliApp ( $queueApp ) or count ( $queueWords ) > 1 )
    return cliFail ( "there is no application named '$queueApp' - pad queue <app> [--failed] [--retry=<id|all>] [--flush[=queue]]" );

  $queueTask = function () use ( $queueArgs, $queueApp ) {

    $retry = cliOption ( $queueArgs, 'retry' );
    $flush = cliOption ( $queueArgs, 'flush' );

    if ( $retry !== NULL ) {

      if ( $retry !== 'all' and ! padQueueIdValid ( $retry ) )
        return cliFail ( "queue: --retry takes the id of a failed job, or all - not '$retry'" );

      $count = padQueueRetry ( $retry );

      if ( ! $count and $retry !== 'all' )
        return cliFail ( "queue: $queueApp has no failed job $retry - pad queue $queueApp --failed lists them" );

      cliOut ( "$count job(s) back on their queue" );

      return 0;

    }

    if ( $flush !== NULL ) {

      $what = ( $flush === TRUE ) ? 'failed' : $flush;

      if ( $what !== 'failed' and ! padQueueNameValid ( $what ) )
        return cliFail ( "queue: '$what' is no queue" );

      $count = padQueueFlush ( $what );

      cliOut ( "$count " . ( $what === 'failed' ? 'failed job(s)' : "waiting job(s) of $what" ) . ' removed' );

      return 0;

    }

    $failed = padQueueFailed ();

    if ( cliOption ( $queueArgs, 'failed' ) ) {

      if ( ! $failed ) {
        cliOut ( "$queueApp has no failed jobs" );
        return 0;
      }

      foreach ( $failed as $job )
        cliOut ( sprintf ( '%s  %-10s %-24s %d tries  %s  %s', $job ['id'], $job ['queue'] ?? '', $job ['job'],
                           $job ['attempts'] ?? 0, ( new DateTimeImmutable ( '@' . (int) ( $job ['failed'] ?? 0 ) ) )->setTimezone ( padDateZone () )->format ( 'Y-m-d H:i:s' ), $job ['error'] ?? '' ) );

      return 0;

    }

    $queues = padQueueQueues ();

    if ( ! $queues )
      cliOut ( "$queueApp has nothing queued" );
    else {
      cliOut ( sprintf ( '  %-16s %6s %8s %8s', 'queue', 'due', 'delayed', 'running' ) );
      foreach ( $queues as $name => $sizes )
        cliOut ( sprintf ( '  %-16s %6d %8d %8d', $name, $sizes ['due'], $sizes ['delayed'], $sizes ['running'] ) );
    }

    cliOut ( count ( $failed ) . ' failed' );

    return 0;

  };

  include cliTask ( $queueApp, $queueTask );

  return 0;

?>
