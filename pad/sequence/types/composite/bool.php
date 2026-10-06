<?php

  // Build strategy 'bool' for the composite sequence: pqBoolComposite() is TRUE for the
  // numbers that have a divisor other than 1 and themselves - 4, 6, 8, 9, 10, 12, 14, ...
  //
  // A whole number from 4 up is composite exactly when it is no prime, so the question goes
  // to prime/bool.php and its gmp_prob_prime(). Trial division by every 6i+/-1 up to the
  // square root found a large prime composite or not only after a billion divisions: a flag
  // over 9223372036854775783 spent the whole time limit on it.

  include_once PT . 'prime/bool.php';

function pqBoolComposite($n, $p=0)
{

    if ( ! pqBoolWhole ( $n ) or $n < 4 )
        return false;

    return ! pqBoolPrime ( $n );
}

?>
