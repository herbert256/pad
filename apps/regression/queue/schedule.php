<?php

  // The scheduler at fixed moments, in a timezone of its own: the entries of _schedule.php
  // with their next run after Wednesday 7 October 2026 07:59, which entries are due at a
  // few moments, and one run of the scheduler at Monday 12 October 08:00 - its record
  // removed first, so every fetch runs it afresh, and the queue it fills emptied after. The
  // entry again asks the scheduler once more while it runs, as a second process in the
  // same minute would: it runs once, and the inner asker finds it ran.

  $padTimezone = 'Europe/Amsterdam';

  $from    = new DateTimeImmutable ( '2026-10-07 07:59', padDateZone () );
  $entries = [];

  foreach ( padScheduleList ( $from ) as $entry )
    $entries [] = [ 'name'  => $entry ['name'],
                    'cron'  => $entry ['cron'],
                    'queue' => $entry ['queue'] === FALSE ? 'inline' : $entry ['queue'],
                    'next'  => date_create ( '@' . $entry ['next'] )->setTimezone ( padDateZone () )->format ( 'D Y-m-d H:i' ) ];

  $faults = padScheduleFaults ();

  $due = [];

  foreach ( [ '2026-10-12 08:00', '2026-10-12 08:15', '2026-10-13 03:00', '2026-11-01 00:00' ] as $at ) {
    $names = [];
    foreach ( padScheduleList () as $entry )
      if ( padScheduleDue ( $entry, new DateTimeImmutable ( $at, padDateZone () ) ) )
        $names [] = $entry ['name'];
    $due [] = [ 'at' => $at, 'names' => implode ( ', ', $names ) ];
  }

  @unlink ( padScheduleFile () );
  padQueueFlush ( 'later' );

  $queueHeard = [];
  $againRuns  = 0;
  $againInner = '';
  $monday     = new DateTimeImmutable ( '2026-10-12 08:00', padDateZone () );
  $ran        = [];

  foreach ( [ 1, 2 ] as $round )
    foreach ( padScheduleRun ( $monday ) as $line )
      $ran [] = [ 'round' => $round, 'name' => $line ['name'], 'status' => $line ['status'] ];

  $heard = implode ( ', ', $queueHeard );

  @unlink ( padScheduleFile () );

?>
