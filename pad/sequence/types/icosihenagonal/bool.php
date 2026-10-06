<?php

  // Membership test for the icosihenagonal sequence, 1, 21, 60, 118, 195, ...: n(19n -
  // 17)/2 for a whole n from 1 up, decided from the square root by pqBoolFigurate() in
  // lib/sequence.php. build/check.php asks it for {keep icosihenagonal}, {remove
  // icosihenagonal} and {flag icosihenagonal}; without it the check generated the sequence
  // up to the candidate, stopped at the million-candidate ceiling and answered no past it.

  function pqBoolIcosihenagonal ( $n, $p=0 ) {

    return pqBoolFigurate ( $n, 19, 17, 2 );

  }

?>
