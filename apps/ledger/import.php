<?php

  // Transactions from a CSV file - a bank's export, or this application's own: a header row
  // naming day, amount, category (and account and note when there are any). A category not
  // known yet is made, as spending when its amounts are below zero; a day that is no date,
  // or an amount that is no number, skips the row and says which.

  $report = [];

  if ( padPosted ( 'import' ) ) {

    $upload = padUpload ( 'csv', types: [ 'text/csv', 'text/plain', 'application/csv' ], max: '5M', dir: 'ledger/imports' );

    if ( $upload ) {

      $handle  = fopen ( $upload ['path'], 'r' );
      $header  = array_map ( fn ( $h ) => strtolower ( trim ( $h, " \t\n\r\0\x0B\xEF\xBB\xBF" ) ), fgetcsv ( $handle, escape: '' ) ?: [] );
      $line    = 1;
      $added   = 0;
      $account = (int) db ( "FIELD id FROM accounts ORDER BY id LIMIT 1" );

      if ( ! in_array ( 'day', $header ) or ! in_array ( 'amount', $header ) or ! in_array ( 'category', $header ) )
        $report [] = [ 'line' => 1, 'said' => 'The first row must name the columns day, amount and category' ];

      else while ( ( $cells = fgetcsv ( $handle, escape: '' ) ) !== FALSE ) {

        $line++;

        if ( count ( $cells ) == 1 and trim ( $cells [0] ) === '' )
          continue;

        $row    = array_combine ( $header, array_pad ( array_slice ( $cells, 0, count ( $header ) ), count ( $header ), '' ) );
        $amount = str_replace ( [ ',', '€', ' ', '−' ], [ '.', '', '', '-' ], (string) $row ['amount'] );
        $day    = date ( 'Y-m-d', strtotime ( (string) $row ['day'] ) ?: 0 );

        if ( ! strtotime ( (string) $row ['day'] ) or ! is_numeric ( $amount ) ) {
          $report [] = [ 'line' => $line, 'said' => 'skipped - no day or no amount' ];
          continue;
        }

        $name = trim ( (string) $row ['category'] ) ?: 'Other';
        $kind = (float) $amount < 0 ? 'expense' : 'income';

        db ( "INSERT OR IGNORE INTO categories (name, kind) VALUES ('{0}', '{1}')", [ mb_substr ( $name, 0, 40 ), $kind ] );

        $category = (int) db ( "FIELD id FROM categories WHERE name = '{0}'", [ mb_substr ( $name, 0, 40 ) ] );
        $from     = isset ( $row ['account'] ) ? (int) db ( "FIELD id FROM accounts WHERE name = '{0}'", [ trim ( $row ['account'] ) ] ) : 0;

        if ( ledgerBook ( $day, $category, $from ?: $account, $amount, (string) ( $row ['note'] ?? '' ) ) )
          $added++;
        else
          $report [] = [ 'line' => $line, 'said' => "skipped - '$name' is a transfer" ];

      }

      fclose ( $handle );
      @unlink ( $upload ['path'] );

      padFlash ( ( $added == 1 ? 'One transaction' : "$added transactions" ) . ' imported' . ( $report ? ', ' . count ( $report ) . ' rows skipped.' : '.' ) );

      if ( ! $report )
        padRedirect ( 'transactions', [ 'month' => ledgerMonth ( '' ) ] );

    }

  }

  $ledgerPage = 'Import';

?>
