<?php


/**
 * Calculates the nth Moser-de Bruijn sequence term.
 *
 * The Moser-de Bruijn sequence consists of sums of distinct
 * powers of 4. S(2n)=4*S(n), S(2n+1)=4*S(n)+1.
 *
 * @param int $n The index in the sequence.
 *
 * @return int The nth Moser-de Bruijn number.
 */
function pqMoserdebruijn($n)
{

   $S = array();

    $S[0] = 0;
    $S[1] = 1;

    for ( $i = 2; $i <= $n; $i++)
    {
        // S(2 * n) = 4 * S(n)
        if ($i % 2 == 0)
        $S[$i] = 4 * $S[$i / 2];

        // S(2 * n + 1) = 4 * S(n) + 1
        else
        $S[$i] = 4 * $S[ceil($i/2)] + 1;
    }

    return $S[$n];
}

// This code is contributed by anuj_67.

?>