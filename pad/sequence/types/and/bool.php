<?php

  // Membership predicate for and: pqBoolAnd($x, $p) answers whether x is a value of
  // n AND p for some loop value n of 1 or more - the question {keep and=p},
  // {remove and=p} and {flag and=p} ask. Generation itself goes through loop.php, which
  // pqBuild() prefers.
  //
  // A value of n AND p keeps only bits that p has, so x is one exactly when x is a submask
  // of p. Every submask is reached, x itself serving as n, and 0 by any n clear of p.

  function pqBoolAnd ( $x, $p ) {

    if ( ! is_numeric ( $x ) or $x != (int) $x )
      return FALSE;

    $x = (int) $x;
    $p = (int) $p;

    return ( ( $x & $p ) === $x and $x >= 0 );

  }

?>