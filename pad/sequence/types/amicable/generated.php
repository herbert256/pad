<?php

  // Precomputed term cache for the amicable sequence.
  //
  // const PADamicable holds its first 10 terms as a 0-indexed array.
  //
  // Loaded by sequence/build/include.php whenever this sequence is built, then read
  // by sequence/plays/play/order.php (term at index, PADamicable[$pqLoop-1]) and by
  // sequence/build/check.php (membership through an index of it, for a type without a
  // test of its own), so those paths answer from the table instead of recomputing the
  // term. Data only - no logic here.

  const PADamicable = [220,284,1184,1210,2620,2924,5020,5564,6232,6368];

?>
