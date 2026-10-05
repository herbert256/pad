<?php

  // Under ten seconds is just now, then seconds, minutes and hours; from a day on the
  // calendar counts - the day before is yesterday whatever the hour - then days, weeks,
  // months and years.

  $padTimezone = 'UTC';

  padNowFreeze ( '2026-01-15 10:00:00' );

  $ago = [];

  foreach ( [ '2026-01-15 09:59:55', '2026-01-15 09:59:30', '2026-01-15 09:59:00', '2026-01-15 09:30:00',
              '2026-01-15 09:00:00', '2026-01-15 00:00:01', '2026-01-14 10:00:00', '2026-01-14 00:00:00',
              '2026-01-13 23:59:00', '2026-01-10', '2026-01-08 10:00', '2026-01-01', '2025-12-16',
              '2025-12-15', '2025-11-14', '2025-01-15', '2024-01-16', '2016-01-01' ] as $date )
    $ago [] = padAgo ( $date );

  $ago = implode ( ' / ', $ago );

?>
