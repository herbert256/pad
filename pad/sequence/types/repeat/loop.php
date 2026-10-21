<?php

  // Loop build for repeat: returns the parameter for every candidate, ignoring the loop
  // value, so {sequence repeat='x', rows=5} yields that value five times. rows= or to=
  // decides how many terms there are; unique would cut it down to one.

  // FALSE is how the walk says a candidate makes no term: repeat=$v with $v FALSE made none,
  // in silence. The strict check names it.

  if ( $pqParm === FALSE and ( $GLOBALS ['padCheckSyntax'] ?? FALSE ) )
    padError ( "repeat= has FALSE to repeat, which the walk reads as no term" );

  return $pqParm;

?>
