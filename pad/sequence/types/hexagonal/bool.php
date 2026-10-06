<?php

  // Membership test for the hexagonal sequence, 1, 6, 15, 28, 45, ...: n(2n - 1) for a
  // whole n from 1 up, decided from the square root by pqBoolFigurate() in
  // lib/sequence.php. build/check.php asks it for {keep hexagonal}, {remove hexagonal} and
  // {flag hexagonal}; without it the check generated the sequence up to the candidate,
  // stopped at the million-candidate ceiling and answered no past it.

  function pqBoolHexagonal ( $n, $p=0 ) {

    return pqBoolFigurate ( $n, 2, 1, 1 );

  }

?>
