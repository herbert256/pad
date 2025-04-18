<?php
// A optimized school
// method based PHP
// program to check
// if a number is composite.

function pqBoolComposite($n, $p=0)
{
    
    // Corner cases
    if ($n <= 1)
        return false;
    if ($n <= 3)
        return false;

    // This is checked so
    // that we can skip
    // middle five numbers
    // in below loop
    if ($n%2 == 0 || $n % 3 == 0)
        return true;

    for ($i = 5; $i * $i <= $n;
                $i = $i + 6)
        if ($n % $i == 0 || $n % ($i + 2) == 0)
        return true;

    return false;
}

?>