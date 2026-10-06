<?php

  // Membership predicate for pronic: pqBoolPronic($x) is TRUE when x is a pronic (oblong)
  // number i(i+1), the product of two consecutive integers - 0, 2, 6, 12, 20, 30, 42, ... The
  // only file in the type, so it is also the generation path and the whole range is filtered
  // through it.
  //
  // i is the whole part of the square root of x, give or take one, so the root is taken once
  // and the i next to it are tried exactly. Trying every i from 0 up to the root cost a
  // billion multiplications for a number of 19 digits: {sequence fibonacci, keep, pronic}
  // spent the whole time limit on one term.

  function pqBoolPronic($x, $p=0) {

    if ( ! pqBoolWhole ( $x ) or $x < 0 )
      return false;

    $x    = (int) $x;
    $root = (int) sqrt ( (float) $x );

    for ( $i = max ( 0, $root - 1 ); $i <= $root + 1; $i++ )
      if ( $x == $i * ( $i + 1 ) )
        return true;

    return false;

  }

?>
