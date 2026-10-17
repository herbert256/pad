<?php

  // A line of an imported CSV with more cells than the header - an amount with a decimal
  // comma, unquoted - is refused by its line number, where it was cut to the header's
  // length and booked -23 on a category "40"; quoted it is a row, and a line with fewer
  // cells has its last ones empty.

  $file = fopen ( 'php://memory', 'w+' );
  fwrite ( $file, "day,amount,category,note\n2026-09-02,-23,40,Groceries,unquoted\n2026-09-03,\"-23,40\",Groceries,quoted\n2026-09-04,-5,Groceries\n" );
  rewind ( $file );

  [ $header, $rows, $refused ] = ledgerCsv ( $file );

  $shown = implode ( ' ', array_map ( fn ( $line, $row ) => "$line:{$row ['amount']}/{$row ['category']}/{$row ['note']}", array_keys ( $rows ), $rows ) )
         . ' | ' . implode ( ' ', array_keys ( $refused ) );

?>
