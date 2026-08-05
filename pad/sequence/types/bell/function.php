<?php

  // Build strategy 'function' for the bell sequence: pqBell($n) returns the nth Bell
  // number, the number of ways a set of n elements can be partitioned.
  //
  // It builds the Bell triangle - every row starts with the last entry of the row above and
  // each further entry is the sum of its left and upper-left neighbours - and takes the
  // head of row n. Counting from $pqLoop = 1 the terms are 1, 2, 5, 15, 52, 203, 877, ...
  // The whole triangle is rebuilt for every term, so cost grows quadratically; the 25 terms
  // that fit in a PHP integer are cached in generated.php.

 function pqBell ($n)
{

    $bell[0][0] = 1;
    for ($i = 1; $i <= $n; $i++)
    {

        $bell[$i][0] = $bell[$i - 1]
                            [$i - 1];

        for ($j = 1; $j <= $i; $j++)
            $bell[$i][$j] = $bell[$i - 1][$j - 1] +
                                $bell[$i][$j - 1];
    }
    return $bell[$n][0];
}

?>