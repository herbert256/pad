<?php

  // Turns the membership verdict of a 'check' build into the term the run's play mode asks
  // for - the same translation plays/plays.php makes when the sequence is a play instead of
  // the run's own subject.
  //
  // pqBuild() collapses keep, remove and flag onto the one 'check' strategy, so the mode
  // itself travels separately in $pqCheckPlay, set by inits/check/sequence.php and defaulted
  // to keep in inits/vars.php. keep hands a member through and drops the rest, remove is its
  // mirror image, and flag answers 1 or 0 instead of the term.
  //
  // Included as an expression by build/one.php, so its return value is the candidate's fate:
  // FALSE rejects it, TRUE keeps it as $pqLoop, anything else becomes the term itself.

  $pqCheckHit = include PQ . 'build/check.php';

  if     ( $pqCheckPlay == 'remove' ) return ( $pqCheckHit ) ? FALSE : $pqLoop;
  elseif ( $pqCheckPlay == 'flag'   ) return ( $pqCheckHit ) ? 1 : 0;
  else                                return $pqCheckHit;

?>