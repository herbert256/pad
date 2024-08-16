<?php
// Iterative Pell Number Series in PHP

// calculate nth pell number
function padSeqPell($n)
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