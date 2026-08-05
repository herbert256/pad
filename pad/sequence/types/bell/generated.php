<?php

  // Precomputed term cache for the bell sequence.
  //
  // const PADbell holds its first 25 terms as a 0-indexed array.
  //
  // Loaded by sequence/build/include.php whenever this sequence is built, then read
  // by sequence/plays/play/order.php (term at index, PADbell[$pqLoop-1]) and by
  // sequence/build/check.php (in_array membership test), so those paths answer from
  // the table instead of recomputing the term. Data only - no logic here.

  const PADbell = [1,2,5,15,52,203,877,4140,21147,115975,678570,4213597,27644437,190899322,1382958545,10480142147,82864869804,682076806159,5832742205057,51724158235372,474869816156751,4506715738447323,44152005855084346,445958869294805289,4638590332229999353];

?>
