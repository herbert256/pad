<?php

  // The day of an imported row: year first or day first - 02/09/2026 is 2 September, as a
  // European bank writes it - a time behind it left out, a month's name read; a day that is
  // no day is NULL, never booked in another month.

  $days  = [ '2026-09-02', '02/09/2026', '13/09/2026', '2.9.2026', '02-09-2026', '2026/9/2 10:15', 'Sep 3, 2026', '31/02/2026', '09/13/2026', 'soon' ];
  $shown = implode ( ' ', array_map ( fn ( $one ) => ledgerImportDay ( $one ) ?? '-', $days ) );

?>
