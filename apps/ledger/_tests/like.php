<?php

  // A search for % or _ finds the month's transactions whose note holds the character,
  // where it went into LIKE as a wildcard and found every one.

  $month = ledgerMonth ( '' );
  $holds = fn ( $c ) => array_column ( db ( "ARRAY id FROM transactions WHERE day LIKE '{0}-%' AND instr(note, '{1}')", [ $month, $c ] ) ?: [], 'id' );
  $found = fn ( $c ) => array_column ( ledgerRows ( $month, 0, 0, $c ), 'id' );
  $same  = fn ( $a, $b ) => ( sort ( $a ) and sort ( $b ) and $a == $b );

  $shown = ledgerLike ( '100%_a!' ) . ' ' . ( ( $same ( $found ( '%' ), $holds ( '%' ) ) and $same ( $found ( '_' ), $holds ( '_' ) ) ) ? 'as written' : 'as wildcards' );

?>
