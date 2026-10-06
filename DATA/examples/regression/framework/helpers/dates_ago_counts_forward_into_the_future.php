<?php

  // A moment still to come is said the other way round: in 45 seconds, tomorrow, in 1 week.

  $padTimezone = 'UTC';

  padNowFreeze ( '2026-01-15 10:00:00' );

  $ahead = [];

  foreach ( [ '2026-01-15 10:00:05', '2026-01-15 10:00:45', '2026-01-15 10:01:00', '2026-01-15 12:00:00',
              '2026-01-16 08:00:00', '2026-01-16 11:00:00', '2026-01-17 00:00:00', '2026-01-22',
              '2026-02-15', '2027-01-15 10:00:00', '2030-06-01' ] as $date )
    $ahead [] = padAgo ( $date );

  $ahead = implode ( ' / ', $ahead );

?>
