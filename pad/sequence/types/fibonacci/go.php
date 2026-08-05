<?php

  // The two-term recurrence shared by the fibonacci and lucas sequences: returns the sum of
  // the two terms preceding the one being produced, read back from the $pqOrder history
  // that build/one.php appends to as the order build runs.
  //
  // Included as an expression by fibonacci/order.php and lucas/order.php once each has
  // supplied its own two seed terms, so the seeds are all that separate the two sequences.
  // $pqOrder is 0-based while $pqLoop counts terms from 1, hence the shift.

  $padFibonacci = $pqLoop - 1;

  return $pqOrder [$padFibonacci-1] + $pqOrder [$padFibonacci-2];

?>