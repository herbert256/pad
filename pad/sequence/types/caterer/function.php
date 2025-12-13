<?php


/**
 * Solves the Lazy Caterer's problem.
 *
 * Returns the maximum number of pieces a circular pancake
 * can be divided into using n straight cuts.
 * Formula: n*(n+1)/2 + 1
 *
 * @param int $n Number of cuts.
 *
 * @return int Maximum pieces achievable.
 */
function pqCaterer ($n)
{
    // Use the formula
    return ($n * ( $n + 1)) / 2 + 1;
}

// This code is contributed
// by nitin mittal.
?>