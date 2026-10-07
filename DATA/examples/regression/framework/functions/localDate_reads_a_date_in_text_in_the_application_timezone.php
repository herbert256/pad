<?php

  // A date in text is read in the application's timezone, the one localDate writes it in:
  // read with strtotime in the zone the request started with, 2023-11-15 01:00 came out as
  // another hour, and with a zone west of that one as the day before.

  $padTimezone = 'Pacific/Kiritimati';

  $day  = '2023-11-15';
  $time = '2023-11-15 01:00';

?>
