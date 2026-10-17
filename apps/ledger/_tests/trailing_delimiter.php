<?php

  // An imported CSV whose data lines end in a delimiter the header does not have - as bank
  // and spreadsheet exports write them - is read row by row, the empty cells past the header
  // dropped, and a line of commas only is no row; a cell past the header that holds a value
  // is still refused. Every line was refused as "5 cells where the first row names 4".

  $file = fopen ( 'php://memory', 'w+' );
  fwrite ( $file, "day,amount,category,note\n2026-09-02,-10.00,Groceries,market,\n2026-09-03,-4.50,Coffee,,\n,,,,,\n2026-09-05,-23,40,Groceries,market,\n" );
  rewind ( $file );

  [ $header, $rows, $refused ] = ledgerCsv ( $file );

  $shown = implode ( ' ', array_map ( fn ( $line, $row ) => "$line:{$row ['amount']}/{$row ['category']}/{$row ['note']}", array_keys ( $rows ), $rows ) )
         . ' | ' . implode ( ' ', array_keys ( $refused ) );

?>
