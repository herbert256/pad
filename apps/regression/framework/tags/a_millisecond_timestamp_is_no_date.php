<?php

  // A timestamp past the year 9999 - JavaScript's Date.now (), in milliseconds - is no
  // date: written back as 57742-03-07 it was read as 2002, and a calendar of one such row
  // beside 2026 showed ten empty years and left the real day out.

  include_once PAD . 'tags/_lib/chart.php';

  $read = [];

  foreach ( [ '1760000000', '1760000000000' ] as $one ) {
    $stamp   = padChartStamp ( $one );
    $read [] = $one . '=' . ( $stamp === FALSE ? 'no date' : gmdate ( 'Y-m-d', $stamp ) );
  }

  $read = implode ( '; ', $read );

?>
