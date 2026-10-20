<?php

  // Membership predicate for nor: pqBoolNor($x, $p) answers whether x is a value of
  // NOT (n OR p) for some loop value n of 1 or more - the question {keep nor=p},
  // {remove nor=p} and {flag nor=p} ask. Generation itself goes through loop.php, which
  // pqBuild() prefers.
  //
  // The complement of x is what n OR p would have to be, so or/bool.php is asked about it
  // - the covering test and its edges.

  function pqBoolNor ( $x, $p ) {

    if ( ! pqBoolWhole ( $x ) or pqPastInt ( $p ) )
      return FALSE;

    $x = (int) $x;
    $p = (int) $p;

    $y = ~ $x;

    include_once PT . 'or/bool.php';

    return pqBoolOr ( $y, $p );

  }

?>
