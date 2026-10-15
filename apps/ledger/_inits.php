<?php

  // Every page: the database as this version of Ledger wants it (ledgerReady), the month the
  // page is about - the asked one, else the newest with transactions - and the menu.

  ledgerReady ();

  $ledgerPage  = 'Overview';
  $ledgerMonth = ledgerMonth ( padRequest ( 'month', '' ) );
  $month       = $ledgerMonth;                 // {?month} in the links: the month goes along
  $ledgerHere  = $GLOBALS ['padPage'] ?? 'index';
  $ledgerWhere = padWasm () ? 'in your browser - PHP compiled to WebAssembly, the books in IndexedDB' : 'on PHP ' . PHP_VERSION . ', the books in DATA/ledger/ledger.sqlite';

  $menu = [];

  foreach ( [ 'index' => 'Overview', 'transactions' => 'Transactions', 'budgets' => 'Budgets', 'import' => 'Import' ] as $page => $label )
    $menu [] = [ 'page' => $page, 'label' => $label, 'current' => $page == $ledgerHere ? 'page' : 'false' ];

  $monthList = array_map ( fn ( $m ) => [ 'value' => $m, 'label' => ledgerMonthName ( $m ), 'chosen' => $m == $ledgerMonth ], ledgerMonths () );

?>
