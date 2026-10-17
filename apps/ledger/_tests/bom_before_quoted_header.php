<?php

  // An imported CSV saved with a byte order mark that quotes every field - a text editor's
  // or a bank's UTF-8 export - has the header day, amount, category and its rows read, where
  // fgetcsv took the quote behind the mark as text: the header's first name was "day" with
  // its quotes, and the file was refused for not naming the column day.

  $file = fopen ( 'php://memory', 'w+' );
  fwrite ( $file, "\xEF\xBB\xBF\"day\",\"amount\",\"category\"\n\"2026-09-02\",\"-10.00\",\"Groceries\"\n" );
  rewind ( $file );

  [ $header, $rows ] = ledgerCsv ( $file );

  $shown = implode ( ',', $header ) . ' | ' . implode ( ' ', array_map ( fn ( $line, $row ) => "$line:{$row ['day']}/{$row ['amount']}", array_keys ( $rows ), $rows ) );

?>
