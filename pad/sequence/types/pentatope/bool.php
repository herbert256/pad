<?php

  // Membership test for pentatope, 0, 1, 5, 15, 35, 70, ...: the n of a term x is about the
  // fourth root of 24x less 1.5, so the root is taken once in floating point and the
  // pentatope numbers of the positions around it are compared exactly. build/check.php asks
  // it for {keep pentatope}, {remove pentatope} and {flag pentatope}.
  //
  // Not the type's build: pqBuild() ranks function.php before bool.php.

  include_once PT . 'pentatope/function.php';

  function pqBoolPentatope ( $x, $p=0 ) {

    if ( ! pqBoolWhole ( $x ) or $x < 0 )
      return FALSE;

    $x    = (int) $x;
    $from = max ( 0, (int) ( ( 24 * (float) $x ) ** 0.25 ) - 3 );

    for ( $n = $from; $n <= $from + 4; $n++ )
      if ( pqPentatope ( $n ) === $x )
        return TRUE;

    return FALSE;

  }

?>
