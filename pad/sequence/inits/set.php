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
  pqNumber     ( 'sole', $pqSole, NULL );

  // 0 is a value like any other - flag:even sole=0 asks about 0 - where it was taken for no
  // sole= at all and the whole default run came back.

  if ( $pqSole !== NULL )
    $pqFrom = $pqTo = $pqSole;

  $pqType   = $padType   [$pad];
  $pqPrefix = $padPrefix [$pad];
  $pqTag    = $padTag    [$pad];

      if ( pqPlay ( $pqTag  ) ) $pqPlay = $pqTag;
  elseif ( pqPlay ( $pqType ) ) $pqPlay = $pqType;
  else                          $pqPlay = 'make';

  $pqNameGiven = $pqName;

  // Only the tag's own run: a run started from an expression inside {resume} is that
  // expression's - sequence:prime(3) in {resume add=sequence:prime(3)} built the store
  // pushed last and answered its third value, 3.

  // {resume} takes a store it names - pull='s', an option s, a first parameter 's' - and the
  // store pushed last otherwise: the store named was passed over, and {resume s, reverse}
  // reversed the last push.

  if ( $pqEntry == 'tag' and $pqTag == 'resume' ) {

    $pqResumeOf = ( is_string ( $pqPull ) and isset ( $pqStore [$pqPull] ) ) ? $pqPull : '';

    foreach ( $padParms [$pad] as $pqResumeOne )
      if ( ! $pqResumeOf and $pqResumeOne ['padPrmKind'] == 'option' and isset ( $pqStore [ $pqResumeOne ['padPrmName'] ] ) )
        $pqResumeOf = $pqResumeOne ['padPrmName'];

    $pqResumeFirst = $padOpt [$pad] [1] ?? '';

    if ( ! $pqResumeOf and is_string ( $pqResumeFirst ) and isset ( $pqStore [$pqResumeFirst] ) )
      $pqResumeOf = $pqResumeFirst;

    $pqPull  = $pqResumeOf ?: $padLastPush;
    $pqBuild = 'pull';

  } elseif ( $pqPull === TRUE ) {
    $pqPull  = $padLastPush;
    $pqBuild = 'pull';
  }

  if ( is_string ( $pqInc ) and str_contains ( $pqInc, '...' ) ) {
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
