<?php

  // Membership predicate and generation path for squarefree: pqBoolSquarefree($n) is TRUE
  // when no square above 1 divides n - when every prime in its factors, pqFactors() in
  // _lib/factors.php, stands there once - 1, 2, 3, 5, 6, 7, 10, 11, 13, 14, 15, ... The type's
  // only build, so the whole range is filtered through it.

  include_once PQ . '_lib/factors.php';

  function pqBoolSquarefree ( $n, $p=0 ) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    return max ( pqFactors ( $n ) ?: [ 1 ] ) == 1;

  }

?>
