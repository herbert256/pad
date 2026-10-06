<?php

  // Membership test for the heptadecagonal sequence, 1, 17, 48, 94, 155, ...: n(15n - 13)/2
  // for a whole n from 1 up, decided from the square root by pqBoolFigurate() in
  // lib/sequence.php. build/check.php asks it for {keep heptadecagonal}, {remove
  // heptadecagonal} and {flag heptadecagonal}; without it the check generated the sequence
  // up to the candidate, stopped at the million-candidate ceiling and answered no past it.

  function pqBoolHeptadecagonal ( $n, $p=0 ) {

    return pqBoolFigurate ( $n, 15, 13, 2 );

  }

?>
