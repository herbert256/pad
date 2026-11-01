<?php

  // Every page: the database as this version of Ledger wants it (ledgerReady), the month the
  // page is about - the asked one, else the newest with transactions - and the menu of the
  // frame every PAD application shares (_common/_tags/padFrame.pad): the four pages of the
  // books, the month going along, and the page behind the overview's quick entry, which says
  // what it is when it is opened as a page. The frame's bar holds the month and the Add
  // button; each page names itself in $title, the bar's heading.

  ledgerReady ();

  $ledgerMonth = ledgerMonth ( padRequest ( 'month', '' ) );
  $month       = $ledgerMonth;                 // {?month} in the links: the month goes along
  $ledgerHere  = $GLOBALS ['padPage'] ?? 'index';
  $ledgerWhere = padWasm () ? 'in your browser - PHP compiled to WebAssembly, the books in IndexedDB' : 'on PHP ' . PHP_VERSION . ', the books in DATA/ledger/ledger.sqlite';

  $frameMenu = [];

  foreach ( [ 'index'        => [ 'Overview',     'home'   ],
              'transactions' => [ 'Transactions', 'list'   ],
              'budgets'      => [ 'Budgets',      'wallet' ],
              'import'       => [ 'Import',       'upload' ],
              'quick'        => [ 'Quick entry',  'zap'    ] ] as $ledgerTo => [ $ledgerLabel, $ledgerIcon ] ) {

    if ( $ledgerTo == 'quick' )
      $frameMenu [] = [ 'head' => 'Behind the overview' ];

    $frameMenu [] = [ 'link' => "$padGo$ledgerTo&month=" . urlencode ( $ledgerMonth ), 'label' => $ledgerLabel, 'icon' => $ledgerIcon, 'current' => $ledgerTo == $ledgerHere ];

  }

  unset ( $ledgerTo, $ledgerLabel, $ledgerIcon );

  $monthList = array_map ( fn ( $m ) => [ 'value' => $m, 'label' => ledgerMonthName ( $m ), 'chosen' => $m == $ledgerMonth ], ledgerMonths () );

?>
