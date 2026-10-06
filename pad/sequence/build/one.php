<?php

  // Turns one candidate into at most one result term - the heart of the build.
  //
  // Called by both iterators (build/types/type/loop.php and .../fixed.php) with the
  // candidate in $pqLoop. Returns TRUE to keep iterating, FALSE to end the whole build:
  // try limit exhausted, stop= value reached, rows= filled, or a float out of int range.
  //
  // Refreshes a random or store-driven parm, optionally replaces $pqLoop with a random
  // pick, then produces $pq using the strategy named by $pqBuild. Plays get to filter or
  // rewrite $pq next, then minimal/maximal, unique and skip are applied. Accepted terms go
  // to $pqResult, with the pre-plays value in $pqOrgHit and each play's own answer in
  // $pqPlaysHit, which sequence/exits/extra/ exposes as extra fields on the tag's data.
  // An order build also appends every generated term to $pqOrder before any filter, since
  // later terms are computed from earlier ones, and suppresses the terms before from=.

  $pqTries++;

  if ( $pqTries > $pqTry ) {
    $pqTriesOut = TRUE;
    return FALSE;
  }

  // Full before a candidate is even made - a count of 0 asks for no rows at all.

  if ( count ( $pqResult ) >= $pqRows )
    return FALSE;

  if ( $pqRandomParm ) $pqParm = include PQ . 'build/parm.php';
  if ( $pqParmStore  ) $pqParm = include PQ . 'build/store.php';

  // A parameter store that has run out ends the run - see build/store.php, and plays.php
  // for the same in a play.

  if ( $pqParmStore and $pqParm === NULL )
    return FALSE;
  if ( $pqRandomly   ) $pqLoop = include PQ . 'build/randomly/randomly.php';

  // A function type computes the term at a position, and a position is a whole number: at
  // from=2.5 bell read the undefined row 2.5 of its triangle and ended the request.

      if ( pqStore ( $pqBuild ) )  $pq = $pqLoop;
  elseif ($pqBuild == 'bool'    )  $pq = ( 'pqBool' . ucfirst($pqSeq) ) ( $pqLoop, $pqParm );
  elseif ($pqBuild == 'function')  $pq = pqBoolWhole ( $pqLoop ) ? ( 'pq' . ucfirst($pqSeq) ) ( $pqLoop ) : FALSE;
  elseif ($pqBuild == 'check'   )  $pq = include PQ . "build/mode.php";
  elseif ($pqBuild == 'loop'    )  $pq = include PT . "$pqSeq/loop.php";
  elseif ($pqBuild == 'make'    )  $pq = include PT . "$pqSeq/make.php";
  elseif ($pqBuild == 'order'   )  $pq = include PT . "$pqSeq/order.php";

  if     ( $pq === FALSE ) return TRUE;
  elseif ( $pq === TRUE  ) $pq = $pqLoop;

  // A term out of the integer range ends the run as soon as it is made, before a play is
  // handed it: checked only after the plays, keep even was asked about the overflowed float
  // and {sequence fibonacci, keep, even, rows=50} ended the request on "not representable as
  // an int". 2^63 is out of range too, though as a float it compares equal to PHP_INT_MAX -
  // tested with >, {sequence power=2, rows=70} ended on 9.2233720368548E+18. A play can make
  // such a value as well, so the same test follows the plays.

  if ( is_float ( $pq ) and ( $pq < PHP_INT_MIN or $pq >= PHP_INT_MAX ) ) return FALSE;

  // A term that is no number at all - the square root exponentiation=0.5 takes of a negative
  // value - is no term: printed, PHP ended the request on the NAN coerced to a string. Not a
  // play's answer either, below.

  if ( is_float ( $pq ) and is_nan ( $pq ) ) return TRUE;

  $pqOrgSet = $pq;

  // An order build computes each term from the ones before it, so every generated term goes
  // into its history now, before a play or a filter can turn it down: added only once
  // accepted, a rejected term left a hole the next one read - {sequence fibonacci, keep,
  // even} ended on an undefined key.

  if ( $pqBuild == 'order' )
    $pqOrder [] = $pqOrgSet;

  if ( count ( $pqPlays ) ) {
    include PQ . 'plays/plays.php';
    if ( $pq === FALSE )
      return ! $pqPlaysOut;
  }

  if ( is_float ($pq)   and ( $pq < PHP_INT_MIN or $pq >= PHP_INT_MAX ) ) return FALSE;
  if ( is_float ($pq)   and is_nan ( $pq )   ) return TRUE;
  if ( is_numeric ($pq) and $pq < $pqMin       ) return TRUE;
  if ( is_numeric ($pq) and $pq > $pqMax       ) return TRUE;
  if ( $pqUnique and in_array ($pq, $pqResult) ) return TRUE;
  if ( $pqSkip and $pqTries <= $pqSkip )         return TRUE;

  if ( $pqBuild == 'order' and $pqLoop < $pqOrderFrom )
    return TRUE;

  $pqResult [] = $pq;
  $pqOrgHit [] = $pqOrgSet;

  if ( count ( $pqPlays ) )
    $pqPlaysHit [] = $pqPlaysSet;

  if ( is_numeric ($pq) and $pq >= $pqStop     ) return FALSE;
  if ( count($pqResult) >= $pqRows )              return FALSE;

  return TRUE;

?>
