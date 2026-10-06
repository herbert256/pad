<?php

  // Membership test for the enneadecagonal sequence, 1, 19, 54, 106, 175, ...: n(17n -
  // 15)/2 for a whole n from 1 up, decided from the square root by pqBoolFigurate() in
  // lib/sequence.php. build/check.php asks it for {keep enneadecagonal}, {remove
  // enneadecagonal} and {flag enneadecagonal}; without it the check generated the sequence
  // up to the candidate, stopped at the million-candidate ceiling and answered no past it.

  function pqBoolEnneadecagonal ( $n, $p=0 ) {

    return pqBoolFigurate ( $n, 17, 15, 2 );

  }

?>
