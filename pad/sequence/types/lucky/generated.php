<?php

  // Precomputed term cache for the lucky sequence.
  //
  // const PADlucky holds its first 13 terms as a 0-indexed array.
  //
  // Loaded by sequence/build/include.php whenever this sequence is built, then read
  // by sequence/plays/play/order.php (term at index, PADlucky[$pqLoop-1]) and by
  // sequence/build/check.php (in_array membership test), so those paths answer from
  // the table instead of recomputing the term. Data only - no logic here.

  const PADlucky = [1,3,7,15,31,63,127,255,511,1023,2047,4095,8191];

?>
