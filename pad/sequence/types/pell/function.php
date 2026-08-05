<?php

  // Function build for pell: pqPell($n) is the nth Pell number, P(1) = 1, P(2) = 2 and
  // P(n) = 2P(n-1) + P(n-2), giving 1, 2, 5, 12, 29, 70, 169, 408, ...
  //
  // Iterative rather than recursive, but with no memory between calls, so a build of m
  // terms walks the recurrence m times.

function pqPell($n)
{
    if ($n <= 2)
        return $n;

    $a = 1;
    $b = 2;
    for ($i = 3; $i <= $n; $i++)
    {
        $c = 2 * $b + $a;
        $a = $b;
        $b = $c;
    }
    return $b;
}

?>