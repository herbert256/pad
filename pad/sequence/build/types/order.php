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
  //
  // The increment is checked as type/loop.php checks it for every other type - a step below
  // 1 is named, 0 is the walk that never starts - and kept as written: max ( 1, (int) ... )
  // took increment=0.5, -1 and 0 for 1 without a word and increment=2.5 for 2, so fibonacci
  // stood at positions 1 3 5 7 where square stands at 1 6 11 16. A position a fractional step
  // lands on between two whole ones has no term, as for those types (build/one.php steps past
  // it); with a whole step from a fractional from= none is ever whole, and the run gives
  // nothing at once - golomb from=2.5 held a million terms back first.

  if ( ! $pqRandomInc and $pqInc < 1 ) {
    if ( $pqInc != 0 and ( $GLOBALS ['padCheckSyntax'] ?? FALSE ) )
      padError ( "increment= needs a step of 1 or more, not '" . ( $pqInitInc ?? $pqInc ) . "'" );
    return;
  }

  // A random increment by the largest step it can be drawn, as type/loop.php has it: one whose
  // largest step is below 1 is no walk, and the run takes the else branch as square does,
  // where it was taken for steps of 1 - {sequence fibonacci, increment='0...0'} and
  // increment='-3...-1' gave 0 1 1.

  if ( $pqRandomInc ) {
    padSplit ( '...', $pqRandomInc, $pqOrderLow, $pqOrderHigh );
    if ( max ( (int) $pqOrderLow, (int) $pqOrderHigh ) < 1 )
      return;
  }

  $pqOrderInc    = $pqInc;
  $pqOrderRandom = $pqRandomInc;
  $pqOrderNext   = $pqOrderFrom;
  $pqOrderMost   = $GLOBALS ['padSeqMaxTries'] ?? 1000000;

  // A random step is a whole step too - max ( 1, (int) ... ) of a draw - so from a fractional
  // from= no position is ever whole either, and it is told before the first position at 1 or
  // above is worked out, which starts a random walk on a whole one: left to the walk, golomb
  // from=2.5 with increment='1...3' held a million terms back for none, and from=-2.5 gave
  // terms at the whole positions its start was moved to, where increment=1 gives none.

  if ( ( $pqOrderRandom or floor ( $pqOrderInc ) == $pqOrderInc ) and floor ( $pqOrderNext ) != $pqOrderNext )
    return;

  // The first position offered at 1 or above is worked out, not stepped to: one step at a time,
  // from=-2000000000 spent the five seconds a run has before its first term and from=-1e20,
  // which adding 1 does not move, never ended. A random step starts one of its largest steps
  // below 1, so the steps drawn from there are few. Past 2^53, where a float no longer holds
  // every whole number, the steps from= names cannot be told, and the walk starts at 1.

  if ( $pqOrderNext < 1 ) {

    if ( $pqOrderRandom ) {

      padSplit ( '...', $pqOrderRandom, $pqOrderLow, $pqOrderHigh );

      $pqOrderNext = max ( $pqOrderNext, 1 - max ( 1, (int) $pqOrderLow, (int) $pqOrderHigh ) );

      while ( $pqOrderNext < 1 )
        $pqOrderNext += max ( 1, (int) pqRandomParm3 ( $pqOrderRandom ) );

    } elseif ( is_int ( $pqOrderNext ) and is_int ( $pqOrderInc ) and $pqOrderNext > PHP_INT_MIN + $pqOrderInc )

      $pqOrderNext += intdiv ( 1 - $pqOrderNext + $pqOrderInc - 1, $pqOrderInc ) * $pqOrderInc;

    elseif ( $pqOrderNext > -9007199254740992 )

      $pqOrderNext += ceil ( ( 1 - $pqOrderNext ) / $pqOrderInc ) * $pqOrderInc;

    else

      $pqOrderNext = 1;

  }

  $pqRandomInc = FALSE;
  $pqFrom      = 1;
  $pqInc       = 1;

  include PQ . 'build/types/type/loop.php';

?>
