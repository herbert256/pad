<?php

  // Hears every statement db() runs: the SQL as it was sent, the rows and whether a time
  // came with it.

  $GLOBALS ['heardSql'] [] = "$sql / rows $rows / " . ( is_float ( $ms ) ? 'timed' : 'untimed' );

?>
