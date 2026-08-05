<?php

  // Precomputed term cache for the nand sequence.
  //
  // const PADnand is empty (the sequence has no terms in the generated range).
  //
  // Loaded by sequence/build/include.php whenever this sequence is built, then read
  // by sequence/plays/play/order.php (term at index, PADnand[$pqLoop-1]) and by
  // sequence/build/check.php (in_array membership test), so those paths answer from
  // the table instead of recomputing the term. Data only - no logic here.

  const PADnand = [];

?>
