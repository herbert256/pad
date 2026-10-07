<?php

  // Membership test for the multiple sequence: pqBoolMultiple() is TRUE when n is a whole
  // multiple of the parameter. Not the type's build strategy - pqBuild() prefers its
  // loop.php - but the predicate build/check.php calls for {keep multiple=3} and friends.
  //
  // The parameter is the argument, not the $pqParm global: the subsystem's run state lives
  // as plain variables in whatever scope the run is in, and inside a nested pass that scope
  // is not the global one - reading the global here divided by nothing.

  function pqBoolMultiple ( $n, $p=0 ) {

    if ( ! is_numeric ( $n ) or ! $p )
      return FALSE;

    // In whole numbers when both are whole and the step is above 0, as pqCeilMultiple makes
    // the terms: through a float the quotient lost its last digits past 2^53, and every one
    // of 9223372036854775801, ...803 and ...805 was a multiple of 3. A bare multiple is 1.

    if ( $p === TRUE )
      $p = 1;

    if ( pqBoolWhole ( $n ) and pqBoolWhole ( $p ) and $p > 0 )
      return ( (int) $n % (int) $p == 0 );

    return ( $n == ceil ( $n / $p ) * $p );

  }

?>
