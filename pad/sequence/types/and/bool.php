<?php

  // Membership predicate for and: pqBoolAnd($x, $p) answers whether x is a value of
  // n AND p for some loop value n of 1 or more - the question {keep and=p},
  // {remove and=p} and {flag and=p} ask. Generation itself goes through loop.php, which
  // pqBuild() prefers.
  //
  // A value of n AND p keeps only bits that p has, so x is one exactly when x is a submask
  // of p. A submask of 1 or more is reached with x itself as n; 0 only by an n of 1 or more
  // clear of p, so only when p leaves one of the 63 low bits clear - with p -1 or
  // PHP_INT_MAX every n AND p is n, and 0 was taken for a value no run makes. A negative x
  // needs the sign bit in n, and n is 1 or more.

  function pqBoolAnd ( $x, $p ) {

    if ( ! pqBoolWhole ( $x ) or pqPastInt ( $p ) )
      return FALSE;

    $x = (int) $x;
    $p = (int) $p;

    return ( $x & $p ) === $x and ( $x >= 1 or ( $x === 0 and ( $p & PHP_INT_MAX ) !== PHP_INT_MAX ) );

  }

?>
