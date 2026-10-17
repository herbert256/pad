<?php

  // The overview's quick entry. ledger.js posts it as JSON to ?quick&padFormat=json with the
  // session's token in X-CSRF-Token, and gets the booking and the month's new totals back as
  // JSON ($padExpose) - with the month it went into; without JavaScript the form posts here
  // and the page goes back to the overview of that month. Today in the month shown, or the
  // month's last day when that is gone by - today from the newest month, the overview's, also
  // when a new month has no transactions yet.

  $booked = FALSE;
  $error  = '';

  if ( padRequestMethod () == 'POST' ) {

    $amount = str_replace ( ',', '.', trim ( (string) padRequest ( 'amount', '' ) ) );
    $month  = ledgerMonth ( padRequest ( 'month', '' ) );
    $day    = ledgerQuickDay ( $month, ledgerMonth ( '' ), date ( 'Y-m-d' ) );
    $first  = (int) db ( "FIELD id FROM accounts ORDER BY id LIMIT 1" );

    if ( ! preg_match ( '/^[0-9]+(\.[0-9][0-9]?)?$/', $amount ) or (float) $amount <= 0 )
      $error = 'An amount like 12.50, please.';
    elseif ( ! ( $booked = ledgerBook ( $day, padRequest ( 'category', 0 ), $first, $amount, trim ( (string) padRequest ( 'note', '' ) ) ) ) )
      $error = 'That category is not there.';

    // The month the entry went into is the one shown from now on - a new month's first.

    if ( $booked )
      $month = $ledgerMonth = substr ( $day, 0, 7 );

    if ( ! padRequestHas ( 'padFormat' ) ) {
      padFlash ( $error ?: 'Booked.', $error ? 'error' : 'info' );
      padRedirect ( 'index', [ 'month' => $month ] );
    }

  }

  $ok      = $booked !== FALSE;
  $totals  = ledgerTotals ( $ledgerMonth );
  $shown   = [ 'income' => ledgerMoney ( $totals ['income'] ), 'spent' => ledgerMoney ( $totals ['spent'] ),
               'left' => ledgerMoney ( $totals ['left'] ), 'kept' => $totals ['kept'] . '%' ];
  $entry   = $ok ? db ( "RECORD t.id, t.day, t.amount, t.note, c.name AS category FROM transactions t JOIN categories c ON c.id = t.category_id WHERE t.id={0}", [ $booked ] ) : NULL;

  $padExpose  = [ 'ok', 'error', 'entry', 'month', 'totals', 'shown' ];
  $ledgerPage = 'Quick entry';

?>
