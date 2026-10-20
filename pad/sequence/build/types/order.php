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

  // The positions offered are the ones from= and increment= name, as for every type that
  // counts its terms: from= on, a step of increment= - drawn anew at every step when it is
  // random, '1...3'. $pqOrderNext is the next one, build/one.php holds back every other term
  // and takes it back off the try count, so skip= and try= count the positions offered.
  // increment= was set to 1 whatever was asked, {sequence fibonacci, increment=2} gave every
  // term, and a random increment stepped the walk itself, which left holes in the history the
  // recurrence reads - "Undefined array key 1". A position below 1 has no term, so the first
  // one offered is the first of the steps at 1 or above.
  //
  // The terms worked out are held to the ceiling every run has, $pqOrderMost, the try limit
  // no longer counting the ones held back.

  $pqOrderInc    = max ( 1, (int) $pqInc );
  $pqOrderRandom = $pqRandomInc;
  $pqOrderNext   = $pqOrderFrom;
  $pqOrderMost   = $GLOBALS ['padSeqMaxTries'] ?? 1000000;

  while ( $pqOrderNext < 1 )
    $pqOrderNext += $pqOrderRandom ? max ( 1, (int) pqRandomParm3 ( $pqOrderRandom ) ) : $pqOrderInc;

  $pqRandomInc = FALSE;
  $pqFrom      = 1;
  $pqInc       = 1;

  include PQ . 'build/types/type/loop.php';

?>
