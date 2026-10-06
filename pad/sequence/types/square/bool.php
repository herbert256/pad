<?php

  // Membership test for the square sequence, 1, 4, 9, 16, 25, ...: n * n for a whole n from
  // 1 up, decided from the square root by pqBoolFigurate() in lib/sequence.php.
  // build/check.php asks it for {keep square}, {remove square} and {flag square}; without
  // it the check generated the sequence up to the candidate, stopped at the
  // million-candidate ceiling and answered no past it.

  function pqBoolSquare ( $n, $p=0 ) {

    return pqBoolFigurate ( $n, 1, 0, 1 );

  }

?>
