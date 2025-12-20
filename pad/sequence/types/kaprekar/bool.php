<?php

function pqBoolKaprekar($n, $p=0)
{
    if ($n == 1)
    return true;

    $sq_n = $n * $n;
    $count_digits = 0;
    while ($sq_n)
    {
        $count_digits++;
        $sq_n = (int)($sq_n / 10);
    }

    $sq_n1 = $n * $n;

    for ($r_digits = 1;
         $r_digits < $count_digits;
         $r_digits++)
    {
        $eq_parts = pow(10, $r_digits);

        if ($eq_parts == $n)
            continue;

        $sum = (int)($sq_n1 / $eq_parts) +
                     $sq_n1 % $eq_parts;
        if ($sum == $n)
        return true;
    }

    return false;
}

?>
