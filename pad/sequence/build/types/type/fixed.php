<?php

  // The fixed iterator: walks the already-known term list in $pqFixed, offering each entry
  // to build/one.php.
  //
  // Shared by every store-style strategy - fixed, build, given and pull - and by the second
  // pass of the randomly= fallback. Here from/to select a 1-based slice of the list rather
  // than a value range, and the try limit is lifted since nothing is being searched for.
  // An increment greater than 1 is honoured by skipping that many entries after each
  // candidate, unless the increment was already consumed by the sequence type itself; a
  // random increment (increment=a...b) is re-rolled every step.

  include PQ . 'build/randomly/init.php';

  $pqSkipNow   = 0;
  $pqSkipCount = 0;
  $pqTry       = PHP_INT_MAX;
  $pqFixed     = array_values ( $pqFixed );

  foreach ( $pqFixed as $pqKey => $pqLoop ) {

    if ( $pqSkipNow ) {
      $pqSkipCount++;
      if ( $pqSkipCount < $pqInc )
        continue;
      $pqSkipCount = $pqSkipNow = 0;
    }

    if ( $pqKey < $pqFrom - 1 ) continue;
    if ( $pqKey > $pqTo - 1   ) break;

    $pqTries = $pqKey;

    if ( ! include PQ . 'build/one.php')
      break;

    if ( $pqRandomInc )
      $pqInc = pqRandomParm3 ( $pqRandomInc );

    if ( $pqInc > 1 and ! in_array ( 'increment', $pqDone ) )
      $pqSkipNow = 1;

  }

?>