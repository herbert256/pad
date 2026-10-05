<?php

  // Build strategy 'function' for the bell sequence: pqBell($n) returns the nth Bell
  // number, the number of ways a set of n elements can be partitioned.
  //
  // It builds the Bell triangle - every row starts with the last entry of the row above and
  // each further entry is the sum of its left and upper-left neighbours - and takes the
  // head of row n. Counting from $pqLoop = 1 the terms are 1, 2, 5, 15, 52, 203, 877, ...
  // The whole triangle is rebuilt for every term, so cost grows quadratically; the 25 terms
  // that fit in a PHP integer are cached in generated.php.
  //
  // Past the 25th term a Bell number no longer fits a PHP integer, and the triangle for a
  // far term was the whole cost: n = 4000 built 181 MB to answer a float the run then threw
  // away. A term past the 25th answers such a float at once, which build/one.php reads as
  // the end of the run.

 function pqBell ($n)
{

    if ( $n > 25 )
      return (float) PHP_INT_MAX * 2;

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