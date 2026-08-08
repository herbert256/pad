<?php

  // Membership test for the multiple sequence: pqBoolMultiple() is TRUE when n is a whole
  // multiple of the parameter. Not the type's build strategy - pqBuild() prefers its
  // loop.php - but the predicate build/check.php calls for {keep multiple=3} and friends.
  //
  // The parameter is the argument, not the $pqParm global: the subsystem's run state lives
  // as plain variables in whatever scope the run is in, and inside a nested pass that scope
  // is not the global one - reading the global here divided by nothing.

  function pqBoolMultiple ( $n, $p=0 ) {

    if ( ! $p )
      return FALSE;

    return ( $n == ceil ( $n / $p ) * $p );

  }

?>