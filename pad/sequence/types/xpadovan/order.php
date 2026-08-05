<?php

  // Order build for xpadovan: the Padovan sequence, seeded 1, 1, 1 and from the fourth term
  // on a(n) = a(n-2) + a(n-3), the same recurrence as perrin with different seeds, read out
  // of the $pqOrder history build/one.php keeps - 1, 1, 1, 2, 2, 3, 4, 5, 7, 9, 12, 16, ...
  //
  // $pqOrder is 0-indexed and holds the terms produced so far, so a(n-2) is at $pqLoop-3.

  if ( $pqLoop == 1 ) return 1;
  if ( $pqLoop == 2 ) return 1;
  if ( $pqLoop == 3 ) return 1;

  return $pqOrder [$pqLoop - 3] +
         $pqOrder [$pqLoop - 4];

?>