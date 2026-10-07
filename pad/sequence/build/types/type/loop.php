<?php

  // The loop iterator: counts from $pqFrom to $pqTo in steps of $pqInc and offers each
  // value to build/one.php as a candidate.
  //
  // Shared by every computed strategy - loop, make, bool, function, check and order.
  // Stops as soon as build/one.php returns FALSE: rows= filled, the stop= value reached,
  // or the try limit exhausted. A random increment (increment=a...b) is re-rolled after
  // every candidate.
  //
  // An increment below 1 never advances the counter - 0 leaves it where it is and a
  // negative one walks away from the from <= to condition - so the walk would not end.
  // The run returns FALSE instead of starting it, and the tag takes its else branch.
  //
  // A random increment is drawn again after every candidate, so it is the largest it can be
  // drawn that decides: '0...2' advances, though a draw of 0 offers the same candidate again.
  // Its first draw decided instead, and one run in three of increment='0...2' answered nothing.

  if ( $pqRandomInc ) {
    padSplit ( '...', $pqRandomInc, $pqIncLow, $pqIncHigh );
    $pqIncTop = max ( (int) $pqIncLow, (int) $pqIncHigh );
  } else
    $pqIncTop = $pqInc;

  if ( $pqIncTop < 1 )
    return FALSE;

  include PQ . 'build/randomly/init.php';

  $pqGo = $pqFrom;

  while ( $pqGo <= $pqTo ) {

    $pqLoop = $pqGo;

    if ( ! include PQ . 'build/one.php')
      break;

    if ( $pqRandomInc )
      $pqInc = pqRandomParm3 ( $pqRandomInc );

    // Out past the integer range a float no longer moves when the step is added - -1e19 + 1
    // is -1e19 - and the walk offered the same candidate again until the try limit, so
    // {sequence modulo=7, from=-10000000000000000000, rows=3} answered -3 -3 -3. A walk that
    // does not advance has nothing more to offer. A random increment drawn as 0 stands still
    // by intent, and is no such walk: ended there, increment='0...2' answered a term or two
    // where it asked for eight.

    if ( $pqInc >= 1 and $pqGo + $pqInc <= $pqGo )
      break;

    $pqGo = $pqGo + $pqInc;

  }

?>
