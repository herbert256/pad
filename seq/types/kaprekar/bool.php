<?php

function padSeqBoolKaprekar($n)
{
    if ($n == 1)
    return true;
 
    // Count number of digits
    // in square
    $sq_n = $n * $n;
    $count_digits = 0;
    while ($sq_n)
    {
        $count_digits++;
        $sq_n = (int)($sq_n / 10);
    }
 
    $sq_n1 = $n * $n; // Recompute square
                      // as it was changed
 
    // Split the square at different
    // points and see if sum of any
    // pair of splitted numbers is equal to n.
    for ($r_digits = 1;
         $r_digits < $count_digits;
         $r_digits++)
    {
        $eq_parts = pow(10, $r_digits);
 
        // To avoid numbers like
        // 10, 100, 1000 (These are not
        // Kaprekar numbers
        if ($eq_parts == $n)
            continue;
 
        // Find sum of current parts
        // and compare with n
        $sum = (int)($sq_n1 / $eq_parts) +
                     $sq_n1 % $eq_parts;
        if ($sum == $n)
        return true;
    }
 
    // compare with original number
    return false;
}

?>