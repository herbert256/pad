<?php

  // Membership test for the heptagonal sequence, 1, 7, 18, 34, 55, ...: n(5n - 3)/2 for a
  // whole n from 1 up, decided from the square root by pqBoolFigurate() in
  // lib/sequence.php. build/check.php asks it for {keep heptagonal}, {remove heptagonal}
  // and {flag heptagonal}; without it the check generated the sequence up to the candidate,
  // stopped at the million-candidate ceiling and answered no past it.

  function pqBoolHeptagonal ( $n, $p=0 ) {

    return pqBoolFigurate ( $n, 5, 3, 2 );

  }

?>
