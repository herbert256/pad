<?php

  // A value spliced into the SQL of a database tag outside a quoted literal of it is refused
  // (padDbSplice, lib/db.php), but a plain number stands there as itself, and inside a quoted
  // literal a value is text - its quotes PAD's inert stand-ins - so these still read.

  $n     = 2000;
  $who   = 'jim';
  $words = 'jim or 1';

?>
