<?php

  // Build strategy 'bool' for the kaprekar sequence: pqBoolKaprekar() is TRUE when the
  // digits of n squared can be cut into a left and a right part that add back up to n -
  // 297^2 = 88209 and 88 + 209 = 297. The terms are 1, 9, 45, 55, 99, 297, 703, 999, ...
  //
  // It counts the digits of n squared, then tries every cut position, skipping the one
  // whose power of ten equals n itself so that a number like 10 cannot qualify on its
  // trailing zeros. 1 is taken as a member by definition.

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