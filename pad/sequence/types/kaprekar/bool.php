<?php

  // Build strategy 'bool' for the kaprekar sequence: pqBoolKaprekar() is TRUE when the
  // digits of n squared can be cut into a left and a right part that add back up to n -
  // 297^2 = 88209 and 88 + 209 = 297. The terms are 1, 9, 45, 55, 99, 297, 703, 999, ...
  //
  // A cut after k digits from the right is n^2 = a * 10^k + b with a + b = n and a right part
  // b of 1 or more, which comes down to n(n - 1) = a(10^k - 1) for a k with 10^k above n -
  // a lower power leaves no room for b, and that is what keeps 10 out on its trailing zeros.
  // So n is a member when 10^k - 1 divides n(n - 1) for such a k. That is asked of n itself
  // rather than of its square: the square passes PHP_INT_MAX from n = 3037000500, and cutting
  // its digits ended the request on a float that is no int - {sequence fibonacci, keep,
  // kaprekar} reached 4807526976 and stopped there. 1 is taken as a member by definition.
  //
  // 10^19 - 1 is past PHP_INT_MAX itself, so it is asked as its two coprime factors, 9 and
  // the repunit 1111111111111111111.

function pqBoolKaprekar($n, $p=0)
{
    if ( ! pqBoolWhole ( $n ) or $n < 1 )
    return false;

    $n = (int) $n;

    if ($n == 1)
    return true;

    for ( $k = strlen ( (string) $n ); $k <= 19; $k++ )

      if ( $k < 19 ) {
        if ( pqBoolKaprekarDivides ( 10 ** $k - 1, $n ) )
          return true;
      } elseif ( pqBoolKaprekarDivides ( 9, $n ) and pqBoolKaprekarDivides ( 1111111111111111111, $n ) )
        return true;

    return false;
}

  // Whether m divides n(n - 1), without forming the product: n and n - 1 share no factor, so
  // what of m does not divide n has to divide n - 1.

function pqBoolKaprekarDivides ( $m, $n )
{
    $a = $m;
    $b = $n;

    while ( $b ) {
      $t = $a % $b;
      $a = $b;
      $b = $t;
    }

    return ( $n - 1 ) % intdiv ( $m, $a ) == 0;
}

?>
