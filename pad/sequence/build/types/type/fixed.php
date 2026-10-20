<?php

  // The fixed iterator: walks the already-known term list in $pqFixed, offering each entry
  // to build/one.php.
  //
  // Shared by every store-style strategy - fixed, build, given and pull - and by the second
  // pass of the randomly= fallback. Here from/to select a 1-based slice of the list rather
  // than a value range, and the try limit is lifted since nothing is being searched for.
  // An increment greater than 1 is honoured by skipping that many entries after each
  // candidate, unless the increment was already consumed by the sequence type itself; a
  // random increment (increment=a...b) is re-rolled every step. The try counter counts the
  // candidates offered, as in the loop iterator - set to the list position, skip= counted
  // from the start of the list whatever from= or increment= had left out.

  include PQ . 'build/randomly/init.php';

  $pqSkipNow   = 0;
  $pqSkipCount = 0;
  // A list ends with its data, so its candidates are not counted - unless the tag counts them
  // itself: try= is a limit every run has, and {sequence '1..100', keep, prime, try=10} gave
  // all 25 primes where the same over a loop gives 2 3 5 7.

  if ( ! isset ( $pqParms ['try'] ) )
    $pqTry = PHP_INT_MAX;
  $pqFixed     = array_values ( $pqFixed );

  // The walk itself is in build/one.php, which offers the entries one by one: it was this
  // file's own foreach, including build/one.php for every candidate.

  $pqWalk = 'fixed';

  include PQ . 'build/one.php';

?>
