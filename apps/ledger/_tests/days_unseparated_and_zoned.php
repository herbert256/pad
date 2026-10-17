<?php

  // The day of an imported row written without separators - 20260902, as several SEPA
  // exports write it - or as a timestamp with a zone behind its time is booked on that day,
  // where both were skipped as no day; a day first written in eight digits is still none.

  $days  = [ '20260902', '2026-09-03 10:15:00 +0200', '2026-09-04T23:30:00Z', '2026-09-05T08:00:00+02:00', '2026-09-06 07:00 -05:00', '02092026', '20261302' ];
  $shown = implode ( ' ', array_map ( fn ( $one ) => ledgerImportDay ( $one ) ?? '-', $days ) );

?>
