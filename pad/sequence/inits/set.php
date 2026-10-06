<?php

  // Finishes parameter intake: derives the run's identity and resolves the random parameters.
  //
  // sole= collapses from and to onto one value; the level's tag, type and prefix are copied
  // into $pqTag / $pqType / $pqPrefix, which inits/find/ and inits/check/ then read; $pqPlay
  // is fixed to whichever of make, keep, remove or flag the tag or type named, defaulting to
  // make; {resume} and a bare pull= are pointed at the last pushed store with build 'pull'.
  //
  // Finally the random forms are resolved: increment='1...5' is kept in $pqRandomInc so it can
  // be redrawn each iteration, while from, to, increment, rows, stop and skip each have their
  // 'a..b' form turned into a single draw made once for the whole run.

  // A random sole='a..b' is drawn once, before it is copied - copied first, from and to were
  // each drawn on their own below and the single value came out a range, or nothing.

  pqRandomParm ( $pqSole );
  pqNumber     ( 'sole', $pqSole, 0 );

  if ( $pqSole )
    $pqFrom = $pqTo = $pqSole;

  $pqType   = $padType   [$pad];
  $pqPrefix = $padPrefix [$pad];
  $pqTag    = $padTag    [$pad];

      if ( pqPlay ( $pqTag  ) ) $pqPlay = $pqTag;
  elseif ( pqPlay ( $pqType ) ) $pqPlay = $pqType;
  else                          $pqPlay = 'make';

  $pqNameGiven = $pqName;

  if ( $pqTag == 'resume' or $pqPull === TRUE ) {
    $pqPull  = $padLastPush;
    $pqBuild = 'pull';
  }

  if ( str_contains ( $pqInc, '...' ) ) {
    $pqRandomInc = $pqInc;
    $pqInc = pqRandomParm3 ( $pqRandomInc );
  } else
    $pqRandomInc = FALSE;

  pqRandomParm ( $pqFrom );
  pqRandomParm ( $pqTo   );
  pqRandomParm ( $pqInc  );
  pqRandomParm ( $pqRows );
  pqRandomParm ( $pqStop );
  pqRandomParm ( $pqSkip );

  // Each of these is a number, or a range one has just been drawn from. One that is neither
  // - from='abc', a request value that held no number - is named by the strict check and
  // takes its default under the lenient walk: used as it came, from= and increment= ended
  // the request on string - int inside the iterators, to= set a bound no number stays under
  // and ran to the million-candidate ceiling, and skip= or minimal= turned every candidate
  // down.

  pqNumber ( 'from',      $pqFrom, 1           );
  pqNumber ( 'to',        $pqTo,   PHP_INT_MAX );
  pqNumber ( 'increment', $pqInc,  1           );
  pqNumber ( 'rows',      $pqRows, NULL        );
  pqNumber ( 'try',       $pqTry,  0           );
  pqNumber ( 'stop',      $pqStop, PHP_INT_MAX );
  pqNumber ( 'skip',      $pqSkip, 0           );
  pqNumber ( 'minimal',   $pqMin,  PHP_INT_MIN );
  pqNumber ( 'maximal',   $pqMax,  PHP_INT_MAX );

?>
