<?php

  // Precomputed term cache for the carmichael sequence.
  //
  // const PADcarmichael holds its first 7 terms as a 0-indexed array.
  //
  // Loaded by sequence/build/include.php whenever this sequence is built, then read
  // by sequence/plays/play/order.php (term at index, PADcarmichael[$pqLoop-1]) and by
  // sequence/build/check.php (membership through an index of it, for a type without a
  // test of its own), so those paths answer from the table instead of recomputing the
  // term. Data only - no logic here.

  const PADcarmichael = [561,1105,1729,2465,2821,6601,8911];

?>
