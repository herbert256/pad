<?php

  // Membership predicate for nand: pqBoolNand($x, $p) answers whether x is a value of
  // NOT (n AND p) for some loop value n of 1 or more - the question {keep nand=p},
  // {remove nand=p} and {flag nand=p} ask. Generation itself goes through loop.php, which
  // pqBuild() prefers.
  //
  // The complement of x is what n AND p would have to be, so the submask test of and/bool.php
  // is applied to it.

  function pqBoolNand ( $x, $p ) {

    if ( ! is_numeric ( $x ) or $x != (int) $x )
      return FALSE;

    $x = (int) $x;
    $p = (int) $p;

    $y = ~ $x;

    return ( ( $y & $p ) === $y and $y >= 0 );

  }

?>