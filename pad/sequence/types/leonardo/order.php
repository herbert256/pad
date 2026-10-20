<?php

  // Build strategy 'order' for the leonardo sequence: seeds 1 and 1, then every term is the
  // sum of the two before it plus one - 1, 1, 3, 5, 9, 15, 25, 41, 67, ... - the sizes of the
  // heaps of smoothsort. Read back from the $pqOrder history build/one.php appends to, 0-based
  // where $pqLoop counts from 1, as fibonacci/go.php does. generated.php caches the terms
  // that fit in a PHP integer.

  if ( $pqLoop == 1 ) return 1;
  if ( $pqLoop == 2 ) return 1;

  return $pqOrder [$pqLoop - 2] + $pqOrder [$pqLoop - 3] + 1;

?>
