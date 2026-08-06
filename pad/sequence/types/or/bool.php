<?php

  // Membership predicate for or: pqBoolOr($x, $p) answers whether x is a value of
  // n OR p for some loop value n of 1 or more - the question {keep or=p},
  // {remove or=p} and {flag or=p} ask. Generation itself goes through loop.php, which
  // pqBuild() prefers.
  //
  // A value of n OR p carries every bit of p, so x is one exactly when x covers p. Taking n
  // as x reaches it, which needs x itself to be 1 or more.

  function pqBoolOr ( $x, $p ) {

    if ( ! is_numeric ( $x ) or $x != (int) $x )
      return FALSE;

    $x = (int) $x;
    $p = (int) $p;

    return ( ( $x | $p ) === $x and $x >= 1 );

  }

?>