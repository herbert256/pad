<?php

  // Settles the two safety limits: $pqRows, how many values to keep, and $pqTry, how many
  // candidates may be tested to find them.
  //
  // A run that already has a natural end - it pulls a store, names a stop or to value, or
  // builds from a fixed or given array - is let run unbounded. Everything else falls back to
  // the configured $padSeqDefaultRows / $padSeqDefaultTries, so an open-ended generator such
  // as {prime} cannot spin forever.

  // A pulled store and a fixed or given array end with their data, so their candidates are
  // not counted. A given array - the values an action is handed, sequence:sum([..]) - is
  // all of its rows too: it saw its first ten values only, and the sum of 1 to 12 was 55. A
  // fixed type's list keeps the default count, as any type does. A stop or to value is a
  // natural end only when it is reached: those runs, an explicit try= and every other run
  // stop at $padSeqMaxTries candidates at the most.

  $pqMaxTries = $GLOBALS ['padSeqMaxTries'] ?? 1000000;

  if ( $pqPull or $pqBuild == 'fixed' or $pqBuild == 'given' ) {
    if ( ! $pqTry  ) $pqTry  = PHP_INT_MAX ;
  } elseif ( $pqTry )
    $pqTry = min ( $pqTry, $pqMaxTries );

  if ( $pqStop != PHP_INT_MAX or $pqTo != PHP_INT_MAX )
    if ( ! $pqTry  ) $pqTry  = $pqMaxTries ;

  if ( $pqPull or $pqStop != PHP_INT_MAX or $pqTo != PHP_INT_MAX or in_array ( $pqBuild, [ 'build', 'given' ] ) )
    if ( $pqRows === NULL or $pqRows === '' ) $pqRows = PHP_INT_MAX ;

  if ( ! $pqTry  ) $pqTry  = $padSeqDefaultTries;

  // A count of 0 is a count: no rows. Unset - NULL - takes the default. They used to be one
  // and the same, so {sequence $n} with $n 0, {loop 0} and rows=0 all ran ten rows; the
  // engine's own 'no limit' is PHP_INT_MAX now, where it was 0 too.

  if ( $pqRows === NULL or $pqRows === '' )
    $pqRows = $padSeqDefaultRows;

  $pqRows = (int) $pqRows;

?>