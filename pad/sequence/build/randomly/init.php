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

    // The first entry the fixed iterator offers, a whole index inside the list: from=0, a
    // negative or a fractional from= drew index -1 or 0.5 and ended the request on "Undefined
    // array key -1" or the float's deprecation as a key.

    $pqRandomlyStart = max ( 0, (int) ceil ( $pqFrom - 1 ) );
    $pqRandomlyEnd   = $pqTo   - 1;

    if ( $pqRandomlyEnd > count ( $pqFixed ) - 1 )
      $pqRandomlyEnd = count ( $pqFixed ) - 1;

  }

  // Without a to= the window was every integer up to PHP_INT_MAX: {sequence prime,
  // randomly} drew 19-digit candidates. It is the first $padSeqDefaultTries steps from
  // from= instead, the same reach an ordered run has by default.

  if ( ! pqStore ( $pqBuild ) and $pqRandomlyEnd == PHP_INT_MAX )
    $pqRandomlyEnd = $pqRandomlyStart + ( ( $GLOBALS ['padSeqDefaultTries'] ?? 10000 ) - 1 ) * $pqInc;

  // The window is walked in steps of the increment - over a store a whole number of entries,
  // 1 or more, the step the fixed iterator takes itself: increment=0 divided by nothing here
  // and ended the request, and increment=0.5 drew the entry at index 1.5.

  // A range the type built has taken the increment itself (types/range/build.php marks it
  // done, as type/fixed.php reads it), so its list is walked a step of 1: stepped again,
  // {sequence '1..20', randomly, increment=5} drew from the first entry of 1 6 11 16 only.

  $pqRandomlyInc = pqStore ( $pqBuild ) ? max ( 1, (int) ceil ( $pqInc ) ) : $pqInc;

  if ( pqStore ( $pqBuild ) and in_array ( 'increment', $pqDone ) )
    $pqRandomlyInc = 1;

  $pqRandomlySteps = intval ( ( $pqRandomlyEnd - $pqRandomlyStart ) / $pqRandomlyInc );

  // With unique a point of the window is drawn once at most: each iterator offers one draw
  // per point - per entry of a store, per step of a loop - and a repeat that unique turned
  // down was a draw lost, so {sequence '1..10', randomly, unique} gave seven or eight of the
  // ten and {sequence prime, randomly, unique, rows=8, to=30} as few as five of the ten
  // primes. build/randomly/randomly.php draws the steps as a shuffle does, one at a time:
  // $pqRandomlyDrawn of them are out, $pqRandomlySwap holds the ones swapped into their
  // places - a window of any width costs a slot per draw.

  $pqRandomlyOnce  = ( $pqUnique and $pqRandomlySteps >= 0 );
  $pqRandomlyDrawn = 0;
  $pqRandomlySwap  = [];

?>
