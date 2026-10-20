<?php

  // Function build for digitSum: pqDigitSum($n) is the sum of the decimal digits of n - 1, 2,
  // ..., 9, 1, 2, 3, ... from n = 1, 0 at position 0. The digits are taken off with intdiv(),
  // which keeps the last ones past 2^53. A position below 0, or between two, has no term.
  // smith/bool.php adds the digits through it as well.

  function pqDigitSum ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    for ( $n = (int) $n, $sum = 0; $n > 0; $n = intdiv ( $n, 10 ) )
      $sum += $n % 10;

    return $sum;

  }

?>
