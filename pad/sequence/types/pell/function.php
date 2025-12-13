<?php


/**
 * Calculates the nth Pell number.
 *
 * Pell numbers follow P(n) = 2*P(n-1) + P(n-2).
 * Uses iterative approach for efficiency.
 *
 * @param int $n The index in the Pell sequence.
 *
 * @return int The nth Pell number.
 */
function pqPell($n)
{
    if ($n <= 2)
        return $n;

    $a = 1;
    $b = 2;
    $c; $i;
    for ($i = 3; $i <= $n; $i++)
    {
        $c = 2 * $b + $a;
        $a = $b;
        $b = $c;
    }
    return $b;
}

// This code is contributed by Ajit.
?>