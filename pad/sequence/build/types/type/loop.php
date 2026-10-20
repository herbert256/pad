<?php

  // The loop iterator: counts from $pqFrom to $pqTo in steps of $pqInc and offers each
  // value to build/one.php as a candidate.
  //
  // Shared by every computed strategy - loop, make, bool, function, check and order.
  // Stops as soon as build/one.php ends the walk: rows= filled, the stop= value reached,
  // or the try limit exhausted. A random increment (increment=a...b) is re-rolled after
  // every candidate.
  //
  // An increment below 1 is no step a walk takes - 0 leaves the counter where it is, a
  // negative one walks away from the from <= to condition, and one between 0 and 1 offers
  // the types candidates that are no whole number. The run returns FALSE instead of
  // starting it, and the tag takes its else branch.
  //
  // A random increment is drawn again after every candidate, so it is the largest it can be
  // drawn that decides: '0...2' advances, though a draw of 0 offers the same candidate again.
  // Its first draw decided instead, and one run in three of increment='0...2' answered nothing.

  if ( $pqRandomInc ) {
    padSplit ( '...', $pqRandomInc, $pqIncLow, $pqIncHigh );
    $pqIncTop = max ( (int) $pqIncLow, (int) $pqIncHigh );
  } else
    $pqIncTop = $pqInc;

  // A step below 0 is no step a walk takes: multiple=-3, step=-2 and increment=-1 answered
  // nothing in silence, where multiple=0 is named. Strict mode names them - the multiples of
  // -3 are kept by keep, multiple=-3 and a play steps down by step=-2, a walk does not. And
  // so is a step between 0 and 1 - increment=0.5, multiple=0.5, step=0.5 - which answered
  // nothing in silence as well; a range counts in it (padGetRange). A step of 0 stays the
  // walk that never starts, the else branch.

  if ( $pqIncTop < 1 and $pqIncTop != 0 and ! $pqRandomInc and ( $GLOBALS ['padCheckSyntax'] ?? FALSE ) )
    padError ( in_array ( $pqSeq, [ 'multiple', 'step' ] )
               ? "The $pqSeq sequence needs a step of 1 or more, not '$pqInc'"
               : "increment= needs a step of 1 or more, not '" . ( $pqInitInc ?? $pqInc ) . "'" );

  if ( $pqIncTop < 1 )
    return FALSE;

  include PQ . 'build/randomly/init.php';

  // The walk itself is in build/one.php, which offers the counter's values one by one: it was
  // this file's own loop, including build/one.php for every candidate.

  $pqGo   = $pqFrom;
  $pqWalk = 'loop';

  include PQ . 'build/one.php';

?>
