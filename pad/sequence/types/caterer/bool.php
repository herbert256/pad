<?php

  // Membership test for the caterer sequence, 2, 4, 7, 11, 16, ...: n(n + 1)/2 + 1 for a
  // whole n from 1 up, so x is a term when x - 1 is the figurate n(n + 1)/2 that
  // pqBoolFigurate() in lib/sequence.php decides from the square root. build/check.php asks
  // it for {keep caterer}, {remove caterer} and {flag caterer}; without it the check
  // generated the sequence up to the candidate, stopped at the million-candidate ceiling
  // and answered no past it.

  function pqBoolCaterer ( $n, $p=0 ) {

    return pqBoolWhole ( $n ) and pqBoolFigurate ( $n - 1, 1, -1, 2 );

  }

?>
