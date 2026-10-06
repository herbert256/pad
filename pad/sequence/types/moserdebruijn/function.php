<?php

  // Build strategy 'function' for the moserdebruijn sequence, the Moser-de Bruijn numbers -
  // the sums of distinct powers of four, 0, 1, 4, 5, 16, 17, 20, 21, ... S(n) is n's binary
  // digits read in base four: every bit b of n stands for 4^b.
  //
  // It tabulated S(2n) = 4*S(n) and S(2n+1) = 4*S(n) + 1 up from S(0) for every term, so m
  // terms cost m^2/2 steps: sequence:moserdebruijn(30000) ran into the time limit. Reading
  // the bits costs one step per bit. From n = 2^32 the term passes PHP_INT_MAX, which is
  // answered with a float out of the integer range, ending the build as the table's
  // overflowing products did.
  //
  // The table starts at S(0), so a negative position has no term and answers FALSE, which
  // drops the candidate - it read an undefined key and ended the request. So has a position
  // between two whole ones.

function pqMoserdebruijn($n)
{

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    if ( $n >= 4294967296 )
      return (float) PHP_INT_MAX * 2;

    $S = 0;

    for ( $bit = 0, $rest = (int) $n; $rest; $bit++, $rest >>= 1 )
      if ( $rest & 1 )
        $S += 1 << ( 2 * $bit );

    return $S;
}

?>
