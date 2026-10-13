<?php

  // A date field reads a date - a day of a month of a year, with a time and a zone or not,
  // or a timestamp - and nothing else: -44 was tomorrow, 1990s today's clock time in 1990,
  // tomorrow a moment that moved on every request, 2026-02-30 the 2nd of March.

  $read = [];

  foreach ( [ '-44', '1990s', 'tomorrow', '2026-02-30', '2026-10-08', 'Oct 8, 2026', '2026-10-08T10:00:00+00:00' ] as $one ) {
    $stamp  = padChartStamp ( $one );
    $read [] = $one . '=' . ( $stamp === FALSE ? 'no date' : gmdate ( 'Y-m-d H:i', $stamp ) );
  }

  $read = implode ( '; ', $read );

?>
