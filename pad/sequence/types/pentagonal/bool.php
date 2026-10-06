<?php

  // Membership test for the pentagonal sequence, 1, 5, 12, 22, 35, ...: n(3n - 1)/2 for a
  // whole n from 1 up, decided from the square root by pqBoolFigurate() in
  // lib/sequence.php. build/check.php asks it for {keep pentagonal}, {remove pentagonal}
  // and {flag pentagonal}; without it the check generated the sequence up to the candidate,
  // stopped at the million-candidate ceiling and answered no past it.

  function pqBoolPentagonal ( $n, $p=0 ) {

    return pqBoolFigurate ( $n, 3, 1, 2 );

  }

?>
