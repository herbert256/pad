<?php

  // Membership predicate for nor: pqBoolNor($x, $p) answers whether x is a value of
  // NOT (n OR p) for some loop value n of 1 or more - the question {keep nor=p},
  // {remove nor=p} and {flag nor=p} ask. Generation itself goes through loop.php, which
  // pqBuild() prefers.
  //
  // The complement of x is what n OR p would have to be, so the covering test of or/bool.php
  // is applied to it.

  function pqBoolNor ( $x, $p ) {

    if ( ! is_numeric ( $x ) or $x != (int) $x )
      return FALSE;

    $x = (int) $x;
    $p = (int) $p;

    $y = ~ $x;

    return ( ( $y | $p ) === $y and $y >= 1 );

  }

?>