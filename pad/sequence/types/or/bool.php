<?php

  // Membership predicate for or: pqBoolOr($x, $p) answers whether x is a value of
  // n OR p for some loop value n of 1 or more - the question {keep or=p},
  // {remove or=p} and {flag or=p} ask. Generation itself goes through loop.php, which
  // pqBuild() prefers.
  //
  // A value of n OR p carries every bit of p, so x is one exactly when x covers p and some n
  // of 1 or more has the rest of x's bits, x AND NOT p: that rest is n when it is 1 or more,
  // and when it is 0 a bit of p is - any but the sign bit. It needs no sign bit of its own,
  // as n has none. Taking n as x itself, x had to be 1 or more, and with a negative p every
  // value of the run - all negative - was denied.

  function pqBoolOr ( $x, $p ) {

    if ( ! pqBoolWhole ( $x ) or pqPastInt ( $p ) )
      return FALSE;

    $x = (int) $x;
    $p = (int) $p;

    if ( ( $x | $p ) !== $x )
      return FALSE;

    $rest = $x & ~ $p;

    return $rest >= 0 and ( $rest >= 1 or ( $p & PHP_INT_MAX ) !== 0 );

  }

?>
