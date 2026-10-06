<?php

  // Relative text that names a zone - 10:30 UTC, tomorrow UTC - is read in that zone, with
  // the clock running or frozen alike. Frozen, the zone was passed over: 10:30 UTC answered
  // 10:30 in the application's zone, an hour off, and tomorrow UTC its midnight.

  $padTimezone = 'Europe/Amsterdam';

  padNowFreeze ( '2030-03-15 12:00:00' );

  $r = [];

  foreach ( [ '10:30 UTC', 'tomorrow UTC', '10:30 +05:00', 'noon America/New_York', 'tomorrow', '+1 hour' ] as $text )
    $r [$text] = padDateParse ( $text )->format ( 'Y-m-d H:i T' );

  $r = json_encode ( $r );

  padNowFreeze ();

?>
