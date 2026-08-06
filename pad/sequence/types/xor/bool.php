<?php

  // Membership predicate for xor: pqBoolXor($x, $p) answers whether x is a value of
  // n XOR p for some loop value n of 1 or more - the question {keep xor=p},
  // {remove xor=p} and {flag xor=p} ask. Generation itself goes through loop.php, which
  // pqBuild() prefers.
  //
  // XOR undoes itself, so the only n that could give x is x XOR p, and x is a value exactly
  // when that n is 1 or more - which rules out x = p and nothing else.

  function pqBoolXor ( $x, $p ) {

    if ( ! is_numeric ( $x ) or $x != (int) $x )
      return FALSE;

    $x = (int) $x;
    $p = (int) $p;

    return ( ( $x ^ $p ) >= 1 );

  }

?>