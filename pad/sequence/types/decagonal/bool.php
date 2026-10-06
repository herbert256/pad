<?php

  // Membership test for the decagonal sequence, 1, 10, 27, 52, 85, ...: n(4n - 3) for a
  // whole n from 1 up, decided from the square root by pqBoolFigurate() in
  // lib/sequence.php. build/check.php asks it for {keep decagonal}, {remove decagonal} and
  // {flag decagonal}; without it the check generated the sequence up to the candidate,
  // stopped at the million-candidate ceiling and answered no past it.

  function pqBoolDecagonal ( $n, $p=0 ) {

    return pqBoolFigurate ( $n, 4, 3, 1 );

  }

?>
