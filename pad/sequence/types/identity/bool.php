<?php

  // Membership test for the identity sequence, the counter 1, 2, 3, ...: a whole number from
  // 1 up. build/check.php asks it for {keep identity}, {remove identity} and {flag
  // identity}; without it the check generated the counter up to the candidate, stopped at
  // the million-candidate ceiling and answered no past it - 2000000 was no term.

  function pqBoolIdentity ( $n, $p=0 ) {

    return pqBoolWhole ( $n ) and $n >= 1;

  }

?>
