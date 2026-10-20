<?php

  // Function build for partition: pqPartition($n) is the number of ways to write n as a sum of
  // whole numbers, the order not counting - 1, 2, 3, 5, 7, 11, 15, 22, 30, 42, ... from n = 1,
  // 1 at position 0 - by Euler's pentagonal number theorem: p(n) is p(n-1) + p(n-2) - p(n-5) -
  // p(n-7) + p(n-12) + p(n-15) - ..., the offsets the pentagonal numbers k(3k-1)/2 and
  // k(3k+1)/2.
  //
  // The terms are added in pairs of one plus and the minus after it, each pair a positive
  // difference, so the running sum only climbs to p(n) and stays inside the integer range
  // while the term does - added in the order of the theorem the sum passed it before the
  // minus came. The terms made are kept between calls; p(405) is the last inside the range,
  // and the float after it ends the build.

  function pqPartition ( $n ) {

    static $made = [ 1 ];

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    for ( $n = (int) $n, $m = count ( $made ); $m <= $n; $m++ ) {

      if ( is_float ( $made [$m - 1] ) )
        return $made [$m - 1];

      // A position below 0 counts 0: no key there.

      for ( $sum = 0, $k = 1; ( $plus = intdiv ( $k * ( 3 * $k - 1 ), 2 ) ) <= $m; $k += 2 ) {
        $minus = intdiv ( ( $k + 1 ) * ( 3 * $k + 2 ), 2 );
        $sum  += $made [$m - $plus] - ( $made [$m - $minus] ?? 0 );
        $sum  += ( $made [$m - $plus - $k] ?? 0 ) - ( $made [$m - $minus - $k - 1] ?? 0 );
      }

      $made [] = $sum;

    }

    return $made [$n];

  }

?>
