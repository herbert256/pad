<?php

  // Membership predicate and generation path for triangular: pqBoolTriangular($n) is TRUE
  // when n = k(k+1)/2 for a whole k - 1, 3, 6, 10, 15, 21, 28, ...
  //
  // k is about the square root of 2n, so the root is taken once and the whole numbers next
  // to it are tried exactly, k(k+1)/2 formed by halving the even one of the two first so it
  // stays inside the integer range. Solving k^2 + k - 2n = 0 in floating point instead and
  // asking whether the root came out whole lost the answer once 8n + 1 had more digits than
  // a float holds: past 10^16 the neighbours of a triangular number were triangular too -
  // 500000000499999999 and 500000000500000001 beside 500000000500000000. The only file in
  // the type, so pqBuild() runs the whole range through it.
  //
  // 0 is the term for k = 0, A000217's first, as pqBoolFigurate takes it for the other
  // figurate types and pronic: turned down, {sequence '0;1;3', flag, triangular} answered
  // 0 1 1 where pronic, square and pentagonal flag their 0, and {sequence triangular, from=0}
  // began on 1 where pronic and square begin on 0.

function pqBoolTriangular ($num) {

    if ( ! pqBoolWhole ( $num ) or $num < 0 )
        return false;

    if ( $num == 0 )
        return true;

    $num  = (int) $num;
    $root = (int) sqrt ( 2 * (float) $num );

    for ( $k = max ( 1, $root - 1 ); $k <= $root + 1; $k++ )
      if ( ( ( $k % 2 ) ? $k * intdiv ( $k + 1, 2 ) : intdiv ( $k, 2 ) * ( $k + 1 ) ) == $num )
        return true;

    return false;
}

?>
