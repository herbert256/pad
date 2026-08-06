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
  // An order build also appends every generated term to $pqOrder, since later terms are
  // computed from earlier ones, and suppresses the terms before the requested from=.

  $pqTries++;

  if ( $pqTries > $pqTry )
    return FALSE;

  if ( $pqRandomParm ) $pqParm = include PQ . 'build/parm.php';
  if ( $pqParmStore  ) $pqParm = include PQ . 'build/store.php';
  if ( $pqRandomly   ) $pqLoop = include PQ . 'build/randomly/randomly.php';

      if ( pqStore ( $pqBuild ) )  $pq = $pqLoop;
  elseif ($pqBuild == 'bool'    )  $pq = ( 'pqBool' . ucfirst($pqSeq) ) ( $pqLoop, $pqParm );
  elseif ($pqBuild == 'function')  $pq = ( 'pq'     . ucfirst($pqSeq) ) ( $pqLoop );
  elseif ($pqBuild == 'check'   )  $pq = include PQ . "build/mode.php";
  elseif ($pqBuild == 'loop'    )  $pq = include PT . "$pqSeq/loop.php";
  elseif ($pqBuild == 'make'    )  $pq = include PT . "$pqSeq/make.php";
  elseif ($pqBuild == 'order'   )  $pq = include PT . "$pqSeq/order.php";

  if     ( $pq === FALSE ) return TRUE;
  elseif ( $pq === TRUE  ) $pq = $pqLoop;

  $pqOrgSet = $pq;

  if ( count ( $pqPlays ) ) {
    include PQ . 'plays/plays.php';
    if ( $pq === FALSE )
      return TRUE;
  }

  if ( is_float ($pq)   and $pq < PHP_INT_MIN  ) return FALSE;
  if ( is_float ($pq)   and $pq > PHP_INT_MAX  ) return FALSE;
  if ( is_numeric ($pq) and $pq < $pqMin       ) return TRUE;
  if ( is_numeric ($pq) and $pq > $pqMax       ) return TRUE;
  if ( $pqUnique and in_array ($pq, $pqResult) ) return TRUE;
  if ( $pqSkip and $pqTries <= $pqSkip )         return TRUE;

  if ( $pqBuild == 'order' ) {
    $pqOrder [] = $pqOrgSet;
    if ( $pqLoop < $pqOrderFrom )
      return TRUE;
  }

  $pqResult [] = $pq;
  $pqOrgHit [] = $pqOrgSet;

  if ( count ( $pqPlays ) )
    $pqPlaysHit [] = $pqPlaysSet;

  if ( is_numeric ($pq) and $pq >= $pqStop     ) return FALSE;
  if ( $pqRows and count($pqResult) >= $pqRows ) return FALSE;

  return TRUE;

?>