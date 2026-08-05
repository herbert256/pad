<?php

  // Order build for tribonacci: seeds 0, 0, 1 and from the fourth term on sums the three
  // preceding terms, read back out of the $pqOrder history build/one.php keeps - so
  // 0, 0, 1, 1, 2, 4, 7, 13, 24, 44, 81, ...
  //
  // As with every order build the terms can only be produced from 1 upwards in steps of 1;
  // build/types/order.php forces that and holds back the terms before the requested from=.

  if ( $pqLoop == 1 ) return 0;
  if ( $pqLoop == 2 ) return 0;
  if ( $pqLoop == 3 ) return 1;

  return $pqOrder [$pqLoop - 2] +
         $pqOrder [$pqLoop - 3] +
         $pqOrder [$pqLoop - 4];

?>