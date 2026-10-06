<?php

  // Function build for polite: pqPolite($n) is the nth polite number - a number that can be
  // written as a sum of two or more consecutive positive integers, which is every number
  // except 1 and the powers of two, so 3, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15, 17, ...
  //
  // n + bits(n + bits(n)), with bits(x) the length of x in binary, is the same closed form
  // as floor(n + log2(n + log2(n))) on n+1, in integers. The float logarithms rounded up
  // at the edge of a power of two - term 1048555 came out 1048576, which is 2^20 - and
  // this form matches a brute-force count over the first nine million terms.

  function pqPolite ($n) {

    // A position below the first one, or between two, has no term: the formula answered there
    // with numbers that are no terms of the sequence - pentagonal at -2 and -1 gave 7 and 2.

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;


    $bits = fn ( $x ) => strlen ( decbin ( $x ) );

    $n = (int) $n;

    // A term is at most 64 past its position, so close to PHP_INT_MAX it is out of the integer
    // range: answered as such it ends the build, where n + bits(n) became a float that decbin()
    // ended the request on.

    if ( $n > PHP_INT_MAX - 128 )
      return (float) PHP_INT_MAX * 2;

    return $n + $bits ( $n + $bits ( $n ) );

  }

?>
