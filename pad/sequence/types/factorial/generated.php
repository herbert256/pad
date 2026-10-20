<?php

  // Precomputed term cache for the factorial sequence.
  //
  // const PADfactorial holds its first 20 terms as a 0-indexed array.
  //
  // Loaded by sequence/build/include.php whenever this sequence is built, then read
  // by sequence/plays/play/order.php (term at index, PADfactorial[$pqLoop-1]) and by
  // sequence/build/check.php (membership through an index of it, for a type without a
  // test of its own), so those paths answer from the table instead of recomputing the
  // term. Data only - no logic here.

  const PADfactorial = [1,2,6,24,120,720,5040,40320,362880,3628800,39916800,479001600,6227020800,87178291200,1307674368000,20922789888000,355687428096000,6402373705728000,121645100408832000,2432902008176640000];

?>
