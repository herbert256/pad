<?php

  // A date written without a zone is read as it stands, also when its clock time falls in
  // the hour the application's timezone skips: read in that zone, 2026-03-29 02:30 in
  // Amsterdam became 03:30, and in Santiago, where the summer starts at midnight, the plain
  // date 2026-09-06 became 01:00 of that day - a moment, no longer a day. A date with its
  // offset is still put in the application's timezone.

  $read = [];

  foreach ( [ 'Europe/Amsterdam' => [ '2026-03-29 02:30', '2026-03-29T00:30:00+00:00' ],
              'America/Santiago' => [ '2026-09-06', '2026-09-06 12:00' ] ] as $zone => $dates ) {
    $padTimezone = $zone;
    foreach ( $dates as $one ) {
      $stamp   = padChartStamp ( $one );
      $read [] = $one . '=' . ( $stamp === FALSE ? 'no date' : gmdate ( 'Y-m-d H:i', $stamp ) );
    }
  }

  $padTimezone = 'America/Santiago';

  $agenda = [ [ 'when' => '2026-09-06', 'what' => 'Spring' ] ];

  $read = implode ( '; ', $read );

?>
