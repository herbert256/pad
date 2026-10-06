<?php

  // padNow is a DateTimeImmutable in $padTimezone - read when it is called, so a page may
  // set it for itself - and with a format the formatted text; padToday is midnight of the
  // same day. Without $padTimezone the zone is PHP's own.

  $padTimezone = 'Asia/Tokyo';

  $nowClass  = get_class ( padNow () );
  $nowZone   = padNow ()->getTimezone ()->getName ();
  $nowClose  = abs ( padNow ()->getTimestamp () - time () ) <= 2 ? 'close' : 'far';
  $nowText   = padNow ( 'e' );
  $today     = padToday ( 'H:i:s.u' );
  $todayZone = padToday ()->getTimezone ()->getName ();
  $todayDay  = padToday ( 'Y-m-d' ) === padNow ( 'Y-m-d' ) ? 'same day' : 'other day';

  $padTimezone = '';

  $phpZone   = padNow ( 'e' ) === date_default_timezone_get () ? 'php' : 'other';

?>
