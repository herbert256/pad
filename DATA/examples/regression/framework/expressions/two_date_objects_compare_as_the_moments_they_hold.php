<?php

  // Two date objects of different zones compared with each other: 03:04:05 UTC is after
  // 05:00 at +05:00 (00:00 UTC), and 08:04:05 at +05:00 is the same moment as 03:04:05 UTC.

  $d    = new DateTime ( '2020-01-02 03:04:05+00:00' );
  $e    = new DateTime ( '2020-01-02 05:00:00+05:00' );
  $same = new DateTime ( '2020-01-02 08:04:05+05:00' );

?>
