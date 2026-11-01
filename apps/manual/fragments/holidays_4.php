<?php

  $kingsDay = padIsHoliday ( '2026-04-27', 'nl' ) ? 'a holiday' : 'a workday';
  $april    = padWorkdays ( '2026-04-01', '2026-04-30', 'nl' );
  $after    = padAddWorkdays ( '2026-04-24', 1, 'nl' );
  $bavaria  = padWorkdays ( '2026-01-01', '2026-12-31', 'de-by' );

?>
