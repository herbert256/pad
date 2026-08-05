<?php

  // Precomputed term cache for the kaprekar sequence.
  //
  // const PADkaprekar holds its first 17 terms as a 0-indexed array.
  //
  // Loaded by sequence/build/include.php whenever this sequence is built, then read
  // by sequence/plays/play/order.php (term at index, PADkaprekar[$pqLoop-1]) and by
  // sequence/build/check.php (in_array membership test), so those paths answer from
  // the table instead of recomputing the term. Data only - no logic here.

  const PADkaprekar = [1,9,45,55,99,297,703,999,2223,2728,4879,4950,5050,5292,7272,7777,9999];

?>
