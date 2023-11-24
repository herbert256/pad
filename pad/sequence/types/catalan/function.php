<?php
// PHP program for nth Catalan Number

// Returns value of Binomial
// Coefficient C(n, k)
function padSeqBinomialCoeff($n, $k)
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

// A Binomial coefficient based function
// to find nth catalan number in O(n) time
function padSeqCatalan($n)
{
    // Calculate value of 2nCn
    $c = padSeqBinomialCoeff(2 * ($n), $n);

    // return 2nCn/(n+1)
    return floor($c / ($n + 1));
}

// This code is contributed by Ryuga
?>