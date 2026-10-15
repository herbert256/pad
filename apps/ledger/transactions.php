<?php

  // A month's transactions as one table - sorted and paged on the server by {datatable} -
  // narrowed to a category, an account or a word in the note. The same rows answer as CSV
  // (?transactions&padFormat=csv) and JSON: $padExpose names them.

  $only     = (int) padRequest ( 'category', 0 );
  $from     = (int) padRequest ( 'account', 0 );
  $word     = trim ( (string) padRequest ( 'q', '' ) );

  $rows     = array_map ( fn ( $r ) => array_diff_key ( $r, [ 'id' => 1, 'kind' => 1 ] ), ledgerRows ( $ledgerMonth, $only, $from, $word ) );
  $shown    = count ( $rows );
  $sum      = round ( array_sum ( array_column ( $rows, 'amount' ) ), 2 );

  $categoryList = array_map ( fn ( $c ) => $c + [ 'chosen' => $c ['id'] == $only ], ledgerCategories () );
  $accountList  = array_map ( fn ( $a ) => $a + [ 'chosen' => $a ['id'] == $from ], ledgerAccounts () );
  $monthName    = ledgerMonthName ( $ledgerMonth );
  $csvName      = "ledger-$ledgerMonth.csv";
  $csvQuery     = http_build_query ( [ 'month' => $ledgerMonth, 'category' => $only, 'account' => $from, 'q' => $word, 'padFormat' => 'csv' ] );

  $padExpose  = [ 'rows' ];
  $ledgerPage = "Transactions, $monthName";

?>
