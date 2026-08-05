<?php

  // Build strategy 'function' for the catalan sequence: pqCatalan($n) returns the nth
  // Catalan number C(2n,n)/(n+1), which counts things like the bracketings of n pairs of
  // parentheses. Counting from $pqLoop = 1 the terms are 1, 2, 5, 14, 42, 132, ... - the
  // leading C(0) = 1 of the usual listing is never produced.
  //
  // pqBinomialCoeff() is the multiplicative binomial, using the symmetry C(n,k) = C(n,n-k)
  // to keep k small. Both work through floor() and so in floating point, which is why
  // generated.php stops caching at 35 terms.

function pqBinomialCoeff($n, $k)
{
    $res = 1;

    if ($k > $n - $k)
        $k = $n - $k;

    for ($i = 0; $i < $k; ++$i)
    {
        $res *= ($n - $i);
        $res = floor($res / ($i + 1));
    }

    return $res;
}

function pqCatalan($n)
{

    $c = pqBinomialCoeff(2 * ($n), $n);

    return floor($c / ($n + 1));
}

?>