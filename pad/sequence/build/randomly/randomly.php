<?php

  // Draws one random candidate from the window set up by build/randomly/init.php.
  //
  // Included as an expression by build/one.php when randomly= is on, and returns the value
  // to use as $pqLoop: for a stored sequence the term at a random index of $pqFixed,
  // otherwise a random point on the from/to/increment grid.

  $pqRandomlyRand = $pqRandomlyStart + rand ( 0, $pqRandomlySteps ) * $pqInc;

  if ( pqStore ( $pqBuild ) )
    return $pqFixed [$pqRandomlyRand];
  else
    return $pqRandomlyRand;

?>