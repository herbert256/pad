<?php

  // The day of a written-down transaction as Y-m-d, whatever form the add form's date rule
  // lets through - October 17 2026 was stored as typed and became the month "October";
  // a day that is none is NULL.

  $days  = [ '2026-10-17', 'October 17 2026', '17.10.2026', '10/17/2026', '2026/10/17', '2026-02-30', 'soon', [ 'x' ] ];
  $shown = implode ( ' ', array_map ( fn ( $one ) => ledgerEntryDay ( $one ) ?? '-', $days ) );

?>
