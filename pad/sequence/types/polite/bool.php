<?php

  // Membership test for the polite sequence, 3, 5, 6, 7, 9, 10, ...: every whole number from
  // 3 up that is no power of two - a power of two has no single bit to spare, which is what
  // n & (n - 1) asks. build/check.php asks it for {keep polite}, {remove polite} and {flag
  // polite}; without it the check generated the sequence up to the candidate, stopped at
  // the million-candidate ceiling and answered no past it.

  function pqBoolPolite ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 3 )
      return FALSE;

    $n = (int) $n;

    return ( $n & ( $n - 1 ) ) != 0;

  }

?>
