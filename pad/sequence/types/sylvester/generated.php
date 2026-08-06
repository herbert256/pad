<?php

  // Precomputed term cache for the sylvester sequence.
  //
  // const PADsylvester holds its first 7 terms as a 0-indexed array.
  //
  // Loaded by sequence/build/include.php whenever this sequence is built, then read
  // by sequence/plays/play/order.php (term at index, PADsylvester[$pqLoop-1]) and by
  // sequence/build/check.php (in_array membership test), so those paths answer from
  // the table instead of recomputing the term. Data only - no logic here.

  const PADsylvester = [2,3,7,43,1807,3263443,10650056950807];

?>