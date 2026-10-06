<?php

  // Membership test for the moserdebruijn sequence, 1, 4, 5, 16, 17, 20, ...: the sums of
  // distinct powers of four from 1 up, which are the numbers with no bit set at an odd
  // place - 4^b is bit 2b. build/check.php asks it for {keep moserdebruijn}, {remove
  // moserdebruijn} and {flag moserdebruijn}; without it the check generated the sequence up
  // to the candidate, stopped at the million-candidate ceiling and answered no past it.

  function pqBoolMoserdebruijn ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    return ( (int) $n & 0x2AAAAAAAAAAAAAAA ) == 0;

  }

?>
