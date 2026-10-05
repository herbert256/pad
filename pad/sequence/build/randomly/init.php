<?php

  // Works out the window random picks are drawn from, before either iterator starts.
  //
  // Included first by build/types/type/loop.php and .../fixed.php; does nothing unless
  // randomly= is on. Stores the from/to bounds and the number of increment-sized steps
  // between them as $pqRandomlyStart/$pqRandomlyEnd/$pqRandomlySteps, which
  // build/randomly/randomly.php then draws from. For a stored sequence the window indexes
  // terms rather than naming values, so the upper bound is clamped to the last index of
  // $pqFixed and both step back off the 1-based from= and to= to the 0-based index they
  // stand for - without that the first term of a store could never be drawn, and to=3
  // could draw the fourth.

  if ( ! $pqRandomly )
    return;

  $pqRandomlyStart = $pqFrom;
  $pqRandomlyEnd   = $pqTo;

  if ( pqStore ( $pqBuild ) ) {

    $pqRandomlyStart = $pqFrom - 1;
    $pqRandomlyEnd   = $pqTo   - 1;

    if ( $pqRandomlyEnd > count ( $pqFixed ) - 1 )
      $pqRandomlyEnd = count ( $pqFixed ) - 1;

  }

  // Without a to= the window was every integer up to PHP_INT_MAX: {sequence prime,
  // randomly} drew 19-digit candidates. It is the first $padSeqDefaultTries steps from
  // from= instead, the same reach an ordered run has by default.

  if ( ! pqStore ( $pqBuild ) and $pqRandomlyEnd == PHP_INT_MAX )
    $pqRandomlyEnd = $pqRandomlyStart + ( ( $GLOBALS ['padSeqDefaultTries'] ?? 10000 ) - 1 ) * $pqInc;

  $pqRandomlySteps = intval ( ( $pqRandomlyEnd - $pqRandomlyStart ) / $pqInc );

?>