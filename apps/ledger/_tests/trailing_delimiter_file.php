<?php

  // Whether the lines of an imported CSV end in a delimiter the header does not have is told
  // by the whole file: here they do not, so a line of 5 cells under 4 names - an amount with
  // a decimal comma, unquoted, and an empty note - is refused by its line number. Decided
  // line by line, its empty last cell was taken for a trailing delimiter and dropped: the
  // line booked -23 on a category "40" with the note Groceries.

  $file = fopen ( 'php://memory', 'w+' );
  fwrite ( $file, "day,amount,category,note\n2026-09-02,-10.00,Groceries,market\n2026-09-05,-23,40,Groceries,\n2026-09-06,-4.50,Coffee,\n" );
  rewind ( $file );

  [ $header, $rows, $refused ] = ledgerCsv ( $file );

  $shown = implode ( ' ', array_map ( fn ( $line, $row ) => "$line:{$row ['amount']}/{$row ['category']}/{$row ['note']}", array_keys ( $rows ), $rows ) )
         . ' | ' . implode ( ' ', array_keys ( $refused ) );

?>
