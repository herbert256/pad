<?php

  // An imported CSV with an empty line between its rows and at its end: every row is read,
  // each with its line number - the empty line ended the import in an error.

  $file = fopen ( 'php://memory', 'w+' );
  fwrite ( $file, "Day,Amount,Category\n2026-09-02,-10.00,Groceries\n\n2026-09-03,-11.00,Groceries\n\n" );
  rewind ( $file );

  [ $header, $rows ] = ledgerCsv ( $file );

  $shown = implode ( ',', $header ) . ' | ' . implode ( ' ', array_map ( fn ( $line, $row ) => "$line:" . $row ['day'], array_keys ( $rows ), $rows ) );

?>
