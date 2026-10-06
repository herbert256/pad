<?php

  // Membership test for the gnomonic sequence, the odd numbers 1, 3, 5, 7, ...: an odd whole
  // number from 1 up. build/check.php asks it for {keep gnomonic}, {remove gnomonic} and
  // {flag gnomonic}; without it the check generated the sequence up to the candidate,
  // stopped at the million-candidate ceiling and answered no past it.

  function pqBoolGnomonic ( $n, $p=0 ) {

    return pqBoolWhole ( $n ) and $n >= 1 and ( $n % 2 ) == 1;

  }

?>
