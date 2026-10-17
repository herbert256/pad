<?php

  // time and timestamp are the date pipe under other names: a date object of another zone
  // than the application's is the moment it holds to them too, not its wall-clock text.
  // 03:04:05 at +05:00 is 22:04:05 UTC the day before.

  $padTimezone = 'UTC';

  $d = new DateTime ( '2020-01-02 03:04:05+05:00' );

?>
