<?php

  // A month asked for that has no transactions, or is no month at all, is the newest one.

  $shown = ledgerMonthName ( '2026-09' ) . ' | ' . ( ledgerMonth ( 'nonsense' ) === ledgerMonth ( '' ) ? 'newest' : 'other' );

?>
