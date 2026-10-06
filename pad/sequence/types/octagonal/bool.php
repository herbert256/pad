<?php

  // Membership test for the octagonal sequence, 1, 8, 21, 40, 65, ...: n(3n - 2) for a
  // whole n from 1 up, decided from the square root by pqBoolFigurate() in
  // lib/sequence.php. build/check.php asks it for {keep octagonal}, {remove octagonal} and
  // {flag octagonal}; without it the check generated the sequence up to the candidate,
  // stopped at the million-candidate ceiling and answered no past it.

  function pqBoolOctagonal ( $n, $p=0 ) {

    return pqBoolFigurate ( $n, 3, 2, 1 );

  }

?>
