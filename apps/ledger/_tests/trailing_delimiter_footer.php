<?php

  // A trailing delimiter is told by the lines of at least the header's length: a footer of
  // fewer cells - Total,-14.50, - says nothing about it, and lines that all end in two empty
  // cells past the header lose both. Each file was refused line by line, nothing imported.
  // In a file that does end in a delimiter an unquoted -23,40 looks like a whole line - in a
  // file of one line always - and is refused, where it booked -23 on a category "40".

  $read = function ( $text ) {
    $file = fopen ( 'php://memory', 'w+' );
    fwrite ( $file, $text );
    rewind ( $file );
    [ $header, $rows, $refused ] = ledgerCsv ( $file );
    return implode ( ' ', array_map ( fn ( $line, $row ) => "$line:{$row ['day']}/{$row ['amount']}/{$row ['category']}/{$row ['note']}", array_keys ( $rows ), $rows ) )
         . ' | ' . implode ( ' ', array_keys ( $refused ) );
  };

  $footer = $read ( "day,amount,category,note\n2026-09-02,-10.00,Groceries,market,\n2026-09-03,-4.50,Coffee,,\nTotal,-14.50,\n" );
  $double = $read ( "day,amount,category,note\n2026-09-02,-10.00,Groceries,market,,\n2026-09-03,-4.50,Coffee,,,\n" );
  $single = $read ( "day,amount,category,note\n2026-09-05,-23,40,Groceries,\n" );
  $whole  = $read ( "day,amount,category,note\n2026-09-05,-23.40,Groceries,,\n2026-09-06,-4,Coffee,beans,\n" );

?>
