<?php

  // The month at a glance: what came in, what went out, what is left; six months of it as a
  // chart, this month's spending by category, the budgets, the accounts, the latest entries
  // - and a quick entry that ledger.js posts as JSON, answered by quick.php.

  $totals    = ledgerTotals ( $ledgerMonth );
  $history   = ledgerHistory ( $ledgerMonth );
  $spending  = ledgerSpending ( $ledgerMonth );
  $budgets   = ledgerBudgets ( $ledgerMonth );
  $accounts  = ledgerAccounts ();
  $latest    = array_slice ( array_values ( array_filter ( ledgerRows ( $ledgerMonth ), fn ( $r ) => $r ['kind'] != 'transfer' ) ), 0, 8 );
  $spendable = array_values ( array_filter ( ledgerCategories (), fn ( $c ) => $c ['kind'] == 'expense' ) );
  $monthName = ledgerMonthName ( $ledgerMonth );
  $today     = date ( 'Y-m-d' );

  $ledgerPage = $monthName;

?>
