<?php

  // {$created | ago} is padAgo in a template: a timestamp, a date in text, now frozen or
  // not; ago($end) counts from another moment, and an empty field has no age.

  $padTimezone = 'UTC';

  padNowFreeze ( '2026-01-15 10:00:00' );

  $created = '2026-01-15 09:55:00';
  $stamp   = padNow ()->getTimestamp () - 7200;
  $start   = '2026-01-01';
  $end     = '2026-01-02';
  $empty   = '';

?>
