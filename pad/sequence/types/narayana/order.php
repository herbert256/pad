<?php

  // Build strategy 'order' for the narayana sequence, Narayana's cows: seeds 1, 1 and 1, then
  // every term is the one before it plus the one three back - 1, 1, 1, 2, 3, 4, 6, 9, 13, 19,
  // 28, ... - a cow calving every year from its fourth on. Read back from the $pqOrder history
  // build/one.php appends to, 0-based where $pqLoop counts from 1, as xpadovan/order.php does.
  // generated.php caches the terms that fit in a PHP integer.

  if ( $pqLoop == 1 ) return 1;
  if ( $pqLoop == 2 ) return 1;
  if ( $pqLoop == 3 ) return 1;

  return $pqOrder [$pqLoop - 2] + $pqOrder [$pqLoop - 4];

?>
