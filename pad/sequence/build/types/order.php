<?php

  // Build strategy 'order': each term is computed from the terms before it (fibonacci and
  // the other recurrences), so the sequence can only be generated from term 1 upwards in
  // steps of 1, whatever from= and increment= asked for.
  //
  // Starts the $pqOrder history the type's order.php reads back, keeps the requested start
  // in $pqOrderFrom so build/one.php can compute the early terms without emitting them,
  // then forces from/increment and runs the loop iterator.

  $pqOrder     = [];
  $pqOrderFrom = $pqFrom;

  // The terms before from= are worked out only to feed the recurrence - they are not
  // candidates offered - yet each passes build/one.php and counts as a try. skip= and try=
  // count candidates offered, as every other build counts them from from=, so both move on
  // by the terms worked through first: {sequence fibonacci, from=5, skip=1} skipped nothing,
  // from=5, try=3 found nothing, and the default try limit ran out before from=15000 of
  // golomb was reached. The ceiling every run has stays.

  $pqOrderBefore = max ( 0, (int) $pqOrderFrom - 1 );

  if ( is_numeric ( $pqSkip ) and $pqSkip > 0 )
    $pqSkip += $pqOrderBefore;

  $pqTry = min ( $pqTry + $pqOrderBefore, $GLOBALS ['padSeqMaxTries'] ?? 1000000 );

  $pqFrom = 1;
  $pqInc  = 1;

  include PQ . 'build/types/type/loop.php';

?>
