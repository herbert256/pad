<?php

  // Settles the two safety limits: $pqRows, how many values to keep, and $pqTry, how many
  // candidates may be tested to find them.
  //
  // A run that already has a natural end - it pulls a store, names a stop or to value, or
  // builds from a fixed or given array - is let run unbounded. Everything else falls back to
  // the configured $padSeqDefaultRows / $padSeqDefaultTries, so an open-ended generator such
  // as {prime} cannot spin forever.

  if ( $pqPull or $pqStop != PHP_INT_MAX or $pqTo != PHP_INT_MAX or $pqBuild == 'fixed' )
    if ( ! $pqTry  ) $pqTry  = PHP_INT_MAX ;

  if ( $pqPull or $pqStop != PHP_INT_MAX or $pqTo != PHP_INT_MAX or $pqBuild == 'build' )
    if ( ! $pqRows ) $pqRows = PHP_INT_MAX ;

  if ( ! $pqTry  ) $pqTry  = $padSeqDefaultTries;
  if ( ! $pqRows ) $pqRows = $padSeqDefaultRows;

?>