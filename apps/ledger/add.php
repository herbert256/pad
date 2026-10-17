<?php

  // A transaction written down: a day, an amount, a category, an account, a note. The rules
  // stand on the fields; a post that kept them is booked - its day as Y-m-d, its sign from
  // its category - and the page goes to the overview of its month.

  if ( padPosted ( 'entry' ) ) {

    $day = ledgerEntryDay ( padRequest ( 'day' ) );

    if ( $day and ledgerBook ( $day, padRequest ( 'category' ), padRequest ( 'account' ), str_replace ( ',', '.', padRequest ( 'amount' ) ), padRequest ( 'note', '' ) ) ) {
      padFlash ( 'Booked.' );
      padRedirect ( 'index', [ 'month' => substr ( $day, 0, 7 ) ] );
    }

    padFlash ( 'That category or account is not there.', 'error' );

  }

  $incomeList  = array_values ( array_filter ( ledgerCategories (), fn ( $c ) => $c ['kind'] == 'income' ) );
  $expenseList = array_values ( array_filter ( ledgerCategories (), fn ( $c ) => $c ['kind'] == 'expense' ) );
  $accountList = ledgerAccounts ();
  $dayDefault  = date ( 'Y-m-d' );
  $ledgerPage  = 'Add a transaction';

?>
