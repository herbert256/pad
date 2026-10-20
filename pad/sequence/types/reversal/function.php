<?php

  // Function build for reversal: pqReversal($n) is n with its decimal digits in reverse order
  // - 1, 2, ..., 9, 1, 11, 21, 31, ... from n = 1, 0 at position 0 - by padTypeReverse() in
  // lib/sequence.php, which palindrome and emirp read as well. A reversal past the integer
  // range is the float it overflows to, which ends the build. A position below 0, or between
  // two, has no term.

  function pqReversal ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    return padTypeReverse ( (int) $n );

  }

?>
