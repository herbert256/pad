<?php

  // Build strategy 'order' for the lucas sequence: seeds 1 and 3, then the same two-term
  // recurrence as fibonacci, shared through fibonacci/go.php - 1, 3, 4, 7, 11, 18, 29, 47,
  // ... Generation starts at L(1), so the L(0) = 2 of the usual listing is not produced.
  // generated.php caches the 90 terms that still fit in a PHP integer.

  if ( $pqLoop == 1 ) return 1;
  if ( $pqLoop == 2 ) return 3;

  return include PT . "fibonacci/go.php";

?>