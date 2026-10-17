<?php

  $padTimezone = 'UTC';

  padNowFreeze ( '2026-01-05 03:04:05 UTC' );

  $when = new DateTimeImmutable ( '2026-01-02 03:04:05', new DateTimeZone ( 'UTC' ) );
  $str  = new class { function __toString () { return 'stringy'; } };

?>
