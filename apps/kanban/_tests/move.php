<?php

  // The third card of the first column dropped before the second, again and again: each
  // time it is second, where at the 45th drop the halved gap reached the 14 digits a
  // position is written with and the card went to the top. Inside a transaction that is
  // rolled back - the board is left as it was.

  db ( "FIELD 1" );

  $GLOBALS ['padSqlConnect']->beginTransaction ();

  $order  = fn () => array_values ( array_column ( db ( "ARRAY id FROM cards WHERE column_id=1 ORDER BY position, id" ) ?: [], 'id' ) );
  $second = 0;

  for ( $drop = 1; $drop <= 60; $drop++ ) {
    $now = $order ();
    kanbanMove ( $now [2], 1, $now [1] );
    $second += ( $order () [1] == $now [2] ) ? 1 : 0;
  }

  $GLOBALS ['padSqlConnect']->rollBack ();

  $shown = "$second of 60 drops second";

?>
