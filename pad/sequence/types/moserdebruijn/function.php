<?php

  // Build strategy 'function' for the moserdebruijn sequence, meant to be the Moser-de
  // Bruijn numbers - the sums of distinct powers of four, 0, 1, 4, 5, 16, 17, 20, 21, ... -
  // from S(2n) = 4*S(n) and S(2n+1) = 4*S(n) + 1, tabulated up from the seeds S(0) = 0 and
  // S(1) = 1.
  //
  // Both branches halve the index with intdiv, which is (i-1)/2 for an odd i - the half the
  // recurrence asks for. The whole table is rebuilt from scratch for every term.

function pqMoserdebruijn($n)
{

   $S = array();

    $S[0] = 0;
    $S[1] = 1;

    for ( $i = 2; $i <= $n; $i++)
    {

        if ($i % 2 == 0)
        $S[$i] = 4 * $S[$i / 2];

        else
        $S[$i] = 4 * $S[intdiv($i,2)] + 1;
    }

    return $S[$n];
}

?>