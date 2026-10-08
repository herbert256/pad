<?php

  // The scheduled entries of every application with a _schedule.php (pad/lib/schedule.php),
  // as pad schedule <app> --list tells them - each entry's cron, its next run and its last -
  // and the one cron line that runs them all.

  $title = 'Schedule';

  if ( adminPost () ) {

    $app = adminAppAsked ();

    if ( $app === '' )
      adminDone ( 'Choose an application.', 'schedule', [], 'error' );

    [ $code, $out, $ms ] = adminRun ( [ 'schedule', $app ] );

    adminHistoryAdd ( "pad schedule $app", $code, $ms );

    adminDone ( "pad schedule $app: " . ( $out === '' ? 'nothing was due' : $out ), 'schedule', [], $code ? 'error' : 'ok' );

  }

  $scheduleRows = [];

  foreach ( adminApps () as $name => $one )
    if ( is_file ( $one ['dir'] . '_schedule.php' ) ) {

      [ $code, $out ] = adminRun ( [ 'schedule', $name, '--list' ], 30 );

      $scheduleRows [] = [ 'app' => $name, 'listing' => $out, 'code' => $code ];

    }

  $scheduleCount = count ( $scheduleRows );
  $cronLine      = '* * * * * ' . APPS . 'cli/pad schedule --all';

?>
