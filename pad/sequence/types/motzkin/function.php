<?php

  // Function build for motzkin: pqMotzkin($n) is the nth Motzkin number, the ways to draw
  // chords between n points on a circle that do not cross - 1, 2, 4, 9, 21, 51, 127, ... from
  // n = 1, 1 at position 0 - by M(n) = ((2n+1) M(n-1) + (3n-3) M(n-2)) / (n+2).
  //
  // The products are larger than the term: formed whole they left the integer range at
  // M(42), three terms before M(44), the last inside it. Both terms before are split into
  // their quotient and remainder by n + 2 first, so the sum is formed as the quotient part
  // plus a small rest divided exactly - nothing passes the term itself. The terms made are
  // kept between calls, so a build of m terms is m steps; the float past M(44) ends it.

  function pqMotzkin ( $n ) {

    static $made = [ 1, 1 ];

    if ( ! pqBoolWhole ( $n ) or $n < 0 )
      return FALSE;

    for ( $n = (int) $n, $i = count ( $made ); $i <= $n; $i++ ) {

      [ $x, $y ] = [ $made [$i - 1], $made [$i - 2] ];

      if ( is_float ( $x ) )
        return $x;

      [ $a, $b, $d ] = [ 2 * $i + 1, 3 * $i - 3, $i + 2 ];

      $made [] = $a * intdiv ( $x, $d ) + $b * intdiv ( $y, $d )
               + intdiv ( $a * ( $x % $d ) + $b * ( $y % $d ), $d );

    }

    return $made [$n];

  }

?>
