<?php
// PHP program for nth Catalan Number


/**
 * Calculates binomial coefficient C(n,k).
 *
 * Uses the multiplicative formula to compute n choose k
 * efficiently in O(k) time.
 *
 * @param int $n Total items.
 * @param int $k Items to choose.
 *
 * @return int The binomial coefficient.
 */
function pqBinomialCoeff($n, $k)
{
    $res = 1;

    // Since C(n, k) = C(n, n-k)
    if ($k > $n - $k)
        $k = $n - $k;

    // Calculate value of [n*(n-1)*---*(n-k+1)] /
    //               [k*(k-1)*---*1]
    for ($i = 0; $i < $k; ++$i)
    {
        $res *= ($n - $i);
        $res = floor($res / ($i + 1));
    }

    return $res;
}


/**
 * Calculates the nth Catalan number.
 *
 * Uses the formula C(2n,n)/(n+1) to compute Catalan
 * numbers in O(n) time via binomial coefficients.
 *
 * @param int $n The index in the Catalan sequence.
 *
 * @return int The nth Catalan number.
 */
function pqCatalan($n)
{
    // Calculate value of 2nCn
    $c = pqBinomialCoeff(2 * ($n), $n);

    // return 2nCn/(n+1)
    return floor($c / ($n + 1));
}

// This code is contributed by Ryuga
?>