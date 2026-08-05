<?php

  // Build strategy 'bool' for the composite sequence: pqBoolComposite() is TRUE for the
  // numbers that have a divisor other than 1 and themselves - 4, 6, 8, 9, 10, 12, 14, ...
  //
  // It is the ordinary 6k+/-1 trial division primality test with its verdict inverted: 0, 1
  // and the primes 2 and 3 are turned away first, anything divisible by 2 or 3 is composite,
  // and beyond that only divisors of the form 6i+/-1 up to sqrt(n) need trying.

function pqBoolComposite($n, $p=0)
{

    if ($n <= 1)
        return false;
    if ($n <= 3)
        return false;

    if ($n%2 == 0 || $n % 3 == 0)
        return true;

    for ($i = 5; $i * $i <= $n;
                $i = $i + 6)
        if ($n % $i == 0 || $n % ($i + 2) == 0)
        return true;

    return false;
}

?>