<?php

  // Function build for pell: pqPell($n) is the nth Pell number, P(1) = 1, P(2) = 2 and
  // P(n) = 2P(n-1) + P(n-2), giving 1, 2, 5, 12, 29, 70, 169, 408, ...
  //
  // Iterative rather than recursive, but with no memory between calls, so a build of m
  // terms walks the recurrence m times.

function pqPell($n)
{

    // A position below the first one, or between two, has no term: the formula answered there
    // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    if ($n <= 2)
        return $n;

    $a = 1;
    $b = 2;
    for ($i = 3; $i <= $n; $i++)
    {
        $c = 2 * $b + $a;
        $a = $b;
        $b = $c;

        // Past P(50) the term is a float out of the integer range, which ends the build: going
        // on to n made a far position - from=1000000000000 - loop for ever.

        if ( is_float ( $b ) )
          return $b;
    }
    return $b;
}

?>
