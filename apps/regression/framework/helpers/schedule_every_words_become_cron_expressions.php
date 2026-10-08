<?php

  // The 'every' words of a _schedule.php entry, as the cron expressions they stand for.

  $r = [];

  foreach ( [ 'minute', '5 minutes', 'hour', 'hour at :15', '3 hours', 'day', 'day at 03:00',
              'weekday at 08:30', 'monday at 08:00', 'Sunday', 'week', 'month', 'month at 06:00', 'year' ] as $every )
    $r [$every] = padCronEvery ( $every );

  $r ['due']    = padScheduleDue ( [ 'every' => 'day at 03:00', 'job' => 'x' ], ( new DateTimeImmutable ( '2026-10-12 03:00', padDateZone () ) )->getTimestamp () );
  $r ['notDue'] = padScheduleDue ( [ 'every' => 'day at 03:00', 'job' => 'x' ], ( new DateTimeImmutable ( '2026-10-12 03:01', padDateZone () ) )->getTimestamp () );

  $r = json_encode ( $r );

?>
