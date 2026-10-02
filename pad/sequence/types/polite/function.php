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

    $bits = fn ( $x ) => strlen ( decbin ( $x ) );

    $n = (int) $n;

    return $n + $bits ( $n + $bits ( $n ) );

  }

?>