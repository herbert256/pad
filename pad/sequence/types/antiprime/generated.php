<?php

  // Precomputed term cache for the antiprime sequence.
  //
  // const PADantiprime holds its first 20 terms as a 0-indexed array.
  //
  // Loaded by sequence/build/include.php whenever this sequence is built, then read
  // by sequence/plays/play/order.php (term at index, PADantiprime[$pqLoop-1]) and by
  // sequence/build/check.php (in_array membership test), so those paths answer from
  // the table instead of recomputing the term. Data only - no logic here.

  const PADantiprime = [1,2,4,6,12,24,36,48,60,120,180,240,360,720,840,1260,1680,2520,5040,7560];

?>