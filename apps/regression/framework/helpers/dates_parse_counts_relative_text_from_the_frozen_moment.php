<?php

  // Text that names no date of its own - tomorrow, +1 week, 10:30 - is counted from now,
  // and a frozen now is the one it is counted from; a date written out is that date.

  $padTimezone = 'UTC';

  padNowFreeze ( '2026-01-15 10:00:00' );

  $relative = [];

  foreach ( [ 'now', 'tomorrow', '+1 week', '10:30', 'yesterday noon', 'next monday',
              'first day of next month', '2026-03-01' ] as $text )
    $relative [$text] = padDateParse ( $text )->format ( 'Y-m-d H:i' );

  $relative = json_encode ( $relative );

?>
