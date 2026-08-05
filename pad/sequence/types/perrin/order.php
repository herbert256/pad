<?php

  // Order build for perrin: seeds the recurrence with 3, 0, 2 and from the fourth term on
  // adds the term two back to the term three back, a(n) = a(n-2) + a(n-3), reading both out
  // of the $pqOrder history build/one.php keeps - 3, 0, 2, 3, 2, 5, 5, 7, 10, 12, 17, ...
  //
  // $pqOrder is 0-indexed and holds the terms produced so far, so a(n-2) is at $pqLoop-3.

  if ( $pqLoop == 1 ) return 3;
  if ( $pqLoop == 2 ) return 0;
  if ( $pqLoop == 3 ) return 2;

  return $pqOrder [$pqLoop - 3] +
         $pqOrder [$pqLoop - 4];

?>