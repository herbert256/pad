<?php
// A php program to find
// the solution to The
// Lazy Caterer's Problem

// This function receives
// an integer n and returns
// the maximum number of
// pieces that can be made
// form pancake using n cuts
function padSeqCaterer ($n)
{
    // Use the formula
    return ($n * ( $n + 1)) / 2 + 1;
}

// This code is contributed
// by nitin mittal.
?>