<?php

  // A plan the page's PHP made with date objects: the gantt ended on "could not be
  // converted to string", or found no tasks.

  $utc  = new DateTimeZone ( 'UTC' );
  $plan = [ [ 'task' => 'Design', 'start' => new DateTimeImmutable ( '2026-10-01', $utc ), 'end' => new DateTimeImmutable ( '2026-10-09', $utc ) ],
            [ 'task' => 'Build',  'start' => new DateTimeImmutable ( '2026-10-05', $utc ), 'end' => new DateTimeImmutable ( '2026-10-20', $utc ) ] ];

?>
