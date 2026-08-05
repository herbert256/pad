<?php

  // Build strategy 'loop' for the kynea sequence: each term is the Kynea number
  // (2^n + 1)^2 - 2, built in three steps from a left shift - 7, 23, 79, 287, 1087, 4223,
  // ... Exact terms run out at n = 31, which is where generated.php stops.
  //
  // It works in $pqLoop itself rather than a local, which is safe only because both
  // iterators reassign the candidate before every call.

    $pqLoop = (1 << $pqLoop) + 1;
    $pqLoop = $pqLoop * $pqLoop;
    $pqLoop = $pqLoop - 2;

    return $pqLoop;

?>