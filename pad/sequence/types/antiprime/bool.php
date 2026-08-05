<?php

  // Build strategy 'bool' for the antiprime sequence - the highly composite numbers, those
  // with strictly more divisors than every smaller number: 1, 2, 4, 6, 12, 24, 36, 48, 60,
  // 120, 180, 240, 360, ...
  //
  // pqBoolAntiprime() counts the divisors of n and rejects it as soon as any smaller number
  // matches or beats that count, so a single test costs O(n sqrt n) and the sequence is
  // only practical over small ranges - generated.php caches the 20 terms up to 7560.
  // pqBoolAntiprimeDivisors() is the divisor count itself, walking to sqrt(a) and counting
  // each factor pair once, or once only when the two halves coincide.

  function pqBoolAntiprime ($n, $p=0) {

    $init = pqBoolAntiprimeDivisors ($n);

    for ($i = 1; $i < $n; $i++)
        if (pqBoolAntiprimeDivisors($i) >= $init)
            return false;

    return true;

  }

  function pqBoolAntiprimeDivisors ($a) {

    if ($a == 1)
        return 1;

    $f = 2;

    for ($i = 2; $i * $i <= $a; $i++)
        if ($a % $i == 0)
            if ($i * $i != $a)
                $f += 2;
            else
                $f++;

    return $f;
}

?>