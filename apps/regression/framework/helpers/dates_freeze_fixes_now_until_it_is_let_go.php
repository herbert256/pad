<?php

  // padNowFreeze fixes now for the rest of the request, read the way padDateParse reads a
  // date, and answers the frozen moment; padNow and padToday follow it, in the
  // application's zone whatever zone the moment was frozen in. NULL lets the clock run.

  $padTimezone = 'Europe/Amsterdam';

  $frozen   = padNowFreeze ( '2026-01-15 10:00:00' )->format ( 'Y-m-d H:i:s e' );
  $now      = padNow ( 'Y-m-d H:i:s' );
  $today    = padToday ( 'Y-m-d H:i:s' );
  $same     = ( padNow () == padNow () ) ? 'same' : 'moving';

  $stamp    = padNowFreeze ( 1768471200 )->format ( 'Y-m-d H:i:s e' );
  $object   = padNowFreeze ( new DateTime ( '2020-02-29 12:00:00', new DateTimeZone ( 'UTC' ) ) )->format ( 'H:i e' );
  $shown    = padNow ( 'Y-m-d H:i e' );

  $released = var_export ( padNowFreeze (), TRUE );
  $running  = abs ( padNow ()->getTimestamp () - time () ) <= 2 ? 'running' : 'frozen';

?>
