<?php

  // A date moved by a count of working days - 2026-10-12 +2 weekdays - is no date, as every
  // other relative part makes one none: date_parse keeps the count beside the named
  // weekday, and the exemption for the weekday let it through - the event was put on the
  // 14th. A date that names its weekday is still read.

  include_once PAD . 'tags/_lib/chart.php';

  $read = [];

  foreach ( [ '2026-10-12 +2 weekdays', '2026-10-12 2 weekdays ago', 'Mon, 12 Oct 2026 +1 weekday', 'Mon, 12 Oct 2026', '2026-10-12 +1 week' ] as $one ) {
    $stamp   = padChartStamp ( $one );
    $read [] = $one . '=' . ( $stamp === FALSE ? 'no date' : gmdate ( 'Y-m-d', $stamp ) );
  }

  $read = implode ( '; ', $read );

?>
