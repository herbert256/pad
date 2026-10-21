<?php

  // The day summer time ends has 25 hours: 00:10 and 23:50 of it lie 24 hours and 40
  // minutes apart, past the hours, on the same calendar day - 0 days between the two
  // midnights, which the count of days made 1: yesterday and tomorrow for that same day.

  $padTimezone = 'Europe/Amsterdam';

  $ago = implode ( ' / ', [
    padAgo ( '2026-10-25 00:10:00', '2026-10-25 23:50:00' ),
    padAgo ( '2026-10-25 23:50:00', '2026-10-25 00:10:00' ),
    padAgo ( '2026-10-24 00:10:00', '2026-10-24 23:50:00' ),
    padAgo ( '2026-10-24 23:50:00', '2026-10-25 23:50:00' )
  ] );

?>
