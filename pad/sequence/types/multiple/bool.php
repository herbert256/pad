<?php

  // Membership test for the multiple sequence: pqBoolMultiple() is TRUE when n is a whole
  // multiple of the parameter. Not the type's build strategy - pqBuild() prefers its
  // loop.php - but the predicate build/check.php calls for {keep multiple=3} and friends.
  //
  // It reads $pqParm from the globals rather than the second argument its callers pass, so
  // it answers for whatever parameter the surrounding build or play currently has set.

  function pqBoolMultiple ( $n, $p=0 ) {

    global $pqParm;

    return ( $n == ceil ( $n / $pqParm) * $pqParm );

  }

?>