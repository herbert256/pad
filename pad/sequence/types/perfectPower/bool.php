<?php

  // Membership predicate and generation path for perfectPower: pqBoolPerfectPower($n) is TRUE
  // when n is m^k for whole numbers m and k of 2 or more, or 1 - 1, 4, 8, 9, 16, 25, 27, 32,
  // 36, 49, 64, ... For every k from 2 up to 62 the k-th root is taken in floating point and
  // the whole numbers next to it are raised to k exactly, multiplied out while the power stays
  // at or below n so nothing leaves the integer range. The type's only build, so the whole
  // range is filtered through it.

  function pqBoolPerfectPower ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    $n = (int) $n;

    if ( $n == 1 )
      return TRUE;

    for ( $k = 2; $k < 63 and 2 ** $k <= $n; $k++ ) {

      $root = (int) round ( $n ** ( 1 / $k ) );

      for ( $m = max ( 2, $root - 1 ); $m <= $root + 1; $m++ ) {

        for ( $power = 1, $i = 0; $i < $k and $power <= intdiv ( $n, $m ); $i++ )
          $power *= $m;

        if ( $i == $k and $power == $n )
          return TRUE;

      }

    }

    return FALSE;

  }

?>
