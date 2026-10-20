<?php

  // Precomputed term cache for the derangement sequence.
  //
  // const PADderangement holds its first 20 terms as a 0-indexed array.
  //
  // Loaded by sequence/build/include.php whenever this sequence is built, then read
  // by sequence/plays/play/order.php (term at index, PADderangement[$pqLoop-1]) and by
  // sequence/build/check.php (membership through an index of it, for a type without a
  // test of its own), so those paths answer from the table instead of recomputing the
  // term. Data only - no logic here.

  const PADderangement = [0,1,2,9,44,265,1854,14833,133496,1334961,14684570,176214841,2290792932,32071101049,481066515734,7697064251745,130850092279664,2355301661033953,44750731559645106,895014631192902121];

?>
