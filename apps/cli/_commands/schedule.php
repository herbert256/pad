<?php

  // pad schedule <app>: runs what the application's _schedule.php says is due this minute
  // (pad/lib/schedule.php) - the command one cron line runs every minute:
  //
  //   * * * * *  /path/to/pad/apps/cli/pad schedule --all
  //
  //   pad schedule shop                 the entries due now, each once in its minute
  //   pad schedule --all                the same for every application with a _schedule.php,
  //                                     each in a process of its own, all at once
  //   pad schedule shop --list          every entry: its cron, the next run and the last
  //   pad schedule shop --at=<time>     as if it were then - 2026-10-12 08:00 - to try an
  //                                     entry out; --list with it counts from then
  //
  // A line per entry that was due: done, failed, queued, skipped (its last run still going)
  // or ran (this minute already). Exit status 1 when an entry failed.

  $scheduleArgs  = array_slice ( $argv, 2 );
  $scheduleWords = array_values ( array_filter ( $scheduleArgs, fn ( $one ) => ! str_starts_with ( $one, '--' ) ) );
  $scheduleAll   = in_array ( '--all', $scheduleArgs, TRUE );

  foreach ( $scheduleArgs as $scheduleArg )
    if ( str_starts_with ( $scheduleArg, '--' ) and ! preg_match ( '/^--(all$|list$|at=)/', $scheduleArg ) )
      return cliFail ( "schedule: there is no option '$scheduleArg' - pad schedule <app> [--list] [--at=time], or pad schedule --all" );

  $scheduleAt = cliOption ( $scheduleArgs, 'at' );

  if ( $scheduleAt !== NULL and ( $scheduleAt === TRUE or strtotime ( $scheduleAt ) === FALSE ) )
    return cliFail ( "schedule: --at is a moment, like '2026-10-12 08:00' - not '" . ( $scheduleAt === TRUE ? '' : $scheduleAt ) . "'" );

  // --all: a child process per application, started together - a slow inline job of one
  // application must not make another's entries miss their minute.

  if ( $scheduleAll ) {

    if ( $scheduleWords )
      return cliFail ( "schedule: --all runs every application - pad schedule --all, or pad schedule <app>" );

    $scheduleProcs = [];

    foreach ( scheduleApps () as $scheduleApp )
      $scheduleProcs [$scheduleApp] = proc_open (
        array_merge ( [ cliPhp (), cliScript (), 'schedule', $scheduleApp ], array_values ( array_diff ( $scheduleArgs, [ '--all' ] ) ) ),
        [ 0 => [ 'file', '/dev/null', 'r' ], 1 => [ 'pipe', 'w' ], 2 => [ 'pipe', 'w' ] ], $schedulePipes [$scheduleApp] );

    $scheduleExit = 0;

    foreach ( $scheduleProcs as $scheduleApp => $scheduleProc ) {

      $scheduleOut = stream_get_contents ( $schedulePipes [$scheduleApp] [1] );
      $scheduleErr = stream_get_contents ( $schedulePipes [$scheduleApp] [2] );

      fclose ( $schedulePipes [$scheduleApp] [1] );
      fclose ( $schedulePipes [$scheduleApp] [2] );

      if ( proc_close ( $scheduleProc ) !== 0 )
        $scheduleExit = 1;

      foreach ( array_filter ( explode ( "\n", rtrim ( $scheduleOut . $scheduleErr ) ) ) as $scheduleLine )
        cliOut ( "$scheduleApp  $scheduleLine" );

    }

    return $scheduleExit;

  }

  $scheduleApp = trim ( $scheduleWords [0] ?? '', '/' );

  if ( ! cliApp ( $scheduleApp ) or count ( $scheduleWords ) > 1 )
    return cliFail ( "there is no application named '$scheduleApp' - pad schedule <app> [--list], or pad schedule --all" );

  if ( ! is_file ( cliHome () . "/apps/$scheduleApp/_schedule.php" ) )
    return cliFail ( "$scheduleApp has no schedule - it goes in apps/$scheduleApp/_schedule.php" );

  $scheduleTask = function () use ( $scheduleArgs, $scheduleAt ) {

    $time = ( $scheduleAt === NULL ) ? NULL : new DateTimeImmutable ( $scheduleAt, padDateZone () );

    if ( in_array ( '--list', $scheduleArgs, TRUE ) ) {

      $entries = padScheduleList ( $time );

      cliOut ( sprintf ( '  %-24s %-18s %-17s %s', 'entry', 'cron', 'next', 'last' ) );

      foreach ( $entries as $entry ) {

        $last = $entry ['last']
              ? padCronMoment ( $entry ['last'] ['time'] ?? 0 )->format ( 'Y-m-d H:i' ) . ' ' . ( $entry ['last'] ['status'] ?? '' )
              : '-';

        cliOut ( sprintf ( '  %-24s %-18s %-17s %s',
                           $entry ['name'] . ( $entry ['queue'] !== FALSE ? ' (' . $entry ['queue'] . ')' : '' ),
                           $entry ['cron'],
                           $entry ['next'] ? padCronMoment ( $entry ['next'] )->format ( 'Y-m-d H:i' ) : 'never',
                           $last ) );

      }

      return 0;

    }

    $exit = 0;

    foreach ( padScheduleRun ( $time ) as $line ) {

      if ( $line ['status'] == 'failed' )
        $exit = 1;

      $text = sprintf ( '%s  %-8s %s', padNow ( 'Y-m-d H:i:s' ), $line ['status'], $line ['name'] );

      if ( $line ['status'] == 'done' or $line ['status'] == 'failed' )
        $text .= '  ' . $line ['ms'] . ' ms';

      foreach ( [ 'id', 'error' ] as $more )
        if ( $line [$more] !== '' )
          $text .= '  ' . $line [$more];

      cliOut ( $text );

    }

    return $exit;

  };

  include cliTask ( $scheduleApp, $scheduleTask );

  return 0;


  // Every application with a _schedule.php: a directory under apps/ with an entry point in
  // www/, as pad test --all finds its applications.

  function scheduleApps () {

    $apps = [];
    $home = cliHome ();

    $walk = function ( $dir, $prefix ) use ( &$walk, &$apps, $home ) {

      foreach ( scandir ( $dir ) as $one ) {

        if ( str_starts_with ( $one, '_' ) or str_starts_with ( $one, '.' ) or ! is_dir ( "$dir/$one" ) )
          continue;

        if ( file_exists ( "$home/www/$prefix$one/index.php" ) and is_file ( "$dir/$one/_schedule.php" ) )
          $apps [] = "$prefix$one";

        $walk ( "$dir/$one", "$prefix$one/" );

      }

    };

    $walk ( "$home/apps", '' );

    sort ( $apps );

    return $apps;

  }

?>
