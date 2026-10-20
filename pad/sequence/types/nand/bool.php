<?php

  // Membership predicate for nand: pqBoolNand($x, $p) answers whether x is a value of
  // NOT (n AND p) for some loop value n of 1 or more - the question {keep nand=p},
  // {remove nand=p} and {flag nand=p} ask. Generation itself goes through loop.php, which
  // pqBuild() prefers.
  //
  // The complement of x is what n AND p would have to be, so and/bool.php is asked about it
  // - the submask test and its edges.

  function pqBoolNand ( $x, $p ) {

    if ( ! pqBoolWhole ( $x ) or pqPastInt ( $p ) )
      return FALSE;

    $x = (int) $x;
    $p = (int) $p;

    $y = ~ $x;

    include_once PT . 'and/bool.php';

    return pqBoolAnd ( $y, $p );

  }

?>
