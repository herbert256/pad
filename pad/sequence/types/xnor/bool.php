<?php

  // Membership predicate for xnor: pqBoolXnor($x, $p) answers whether x is a value of
  // NOT (n XOR p) for some loop value n of 1 or more - the question {keep xnor=p},
  // {remove xnor=p} and {flag xnor=p} ask. Generation itself goes through loop.php, which
  // pqBuild() prefers.
  //
  // The complement of x is what n XOR p would have to be, so the n that could give x is that
  // complement XOR p, and x is a value exactly when it is 1 or more.

  function pqBoolXnor ( $x, $p ) {

    if ( ! is_numeric ( $x ) or $x != (int) $x )
      return FALSE;

    $x = (int) $x;
    $p = (int) $p;

    $y = ~ $x;

    return ( ( $y ^ $p ) >= 1 );

  }

?>