<?php

  // The even@tag property: TRUE on every second occurrence, the first counting as odd -
  // the workhorse of zebra striping. odd.php is its negation.

  global $padOccur;

  return ( $padOccur [$padIdx] % 2 == 0 );

?>