<?php

  // Function build for divisors: pqDivisors($n) is how many divisors n has - 1, 2, 2, 3, 2, 4,
  // 2, 4, 3, 4, ... from n = 1: the product of one more than every exponent in its prime
  // factors, which pqFactors() in _lib/factors.php finds. A position below 1, or between two,
  // has no term.

  include_once PQ . '_lib/factors.php';

  function pqDivisors ( $n ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $count = 1;

    foreach ( pqFactors ( $n ) as $e )
      $count *= $e + 1;

    return $count;

  }

?>
