<?php

  // A date that names its weekday - as RSS, mail and HTTP write a date - is read as the
  // date it is: the weekday alone moves nothing, where the check that refuses tomorrow and
  // -44 (251b2820c) took it for a moment counted from now and the date was refused.

  $read = [];

  foreach ( [ 'Mon, 12 Oct 2026 10:00:00 +0000', 'Monday 12 October 2026', 'tomorrow', 'next monday' ] as $one ) {
    $stamp   = padChartStamp ( $one );
    $read [] = $one . '=' . ( $stamp === FALSE ? 'no date' : gmdate ( 'Y-m-d H:i', $stamp ) );
  }

  $read = implode ( '; ', $read );

?>
