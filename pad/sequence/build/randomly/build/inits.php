<?php

  // Arms the two-pass fallback that makes randomly= work on a computed sequence.
  //
  // First half of the pair bracketing the strategy dispatch in build/build.php. Sampling
  // needs a term list to sample from, which only the store-style strategies have, so for a
  // function, bool or order build this saves rows/plays, switches randomly
  // off and raises $pqRandomlyBuild: the build then runs in order and
  // build/randomly/build/exits.php does the sampling. When a to= was given the rows limit
  // is dropped for that first pass, so the whole requested window is generated before it
  // is sampled. Any other strategy is left alone, with $pqRandomlyBuild FALSE.
  //
  // The plays are put aside rather than merely copied: the second pass restores them and
  // runs them over what it samples, so leaving them in place here applied each of them
  // twice - a make=negation came back positive.

  $pqRandomlyBuild = FALSE;
  $pqRandomlyStop  = PHP_INT_MAX;

  if ( ! $pqRandomly )                                        return;
  if ( ! in_array ( $pqBuild, ['function','bool','order'] ) ) return;

  $pqPlaysRandomly = $pqPlays;
  $pqRowsRandomly  = $pqRows;

  $pqPlays         = [];

  // The first pass makes the whole window the draws come from - as far as to=, or the default
  // try count without it, the reach a point-wise draw has: it made rows= terms when no to= was
  // given, so {sequence happy, randomly, rows=5, unique} drew 1 7 10 13 19 every time, and a
  // keep play set aside for the second pass found nothing among the first three Fibonacci
  // numbers.

  $pqRows = PHP_INT_MAX;

  // The bounds, stop= and unique judge the terms the plays make, so with plays put aside they
  // go aside too, and the second pass applies them after the plays: applied to the terms
  // before them, maximal=15 with make, subtract=10 never drew the 21 that makes 11, and
  // stop=10 with make, negation ended the first pass at 13, so -21 and -34 were never drawn.

  $pqBoundsRandomly = [ $pqMin, $pqMax, $pqUnique, $pqStop ];

  if ( count ( $pqPlaysRandomly ) ) {
    $pqMin    = PHP_INT_MIN;
    $pqMax    = PHP_INT_MAX;
    $pqUnique = '';
    $pqStop   = PHP_INT_MAX;
  }

  // The first pass still ends where the ordered run with the plays ends: build/one.php runs
  // them over each term to see whether the term they make reaches the stop= put aside,
  // $pqRandomlyStop. A run that stop= alone bounded - inits/limits.php gives it the
  // ceiling of candidates - makes no more than the window of the default try count, as a run
  // without stop= does, should no played term reach it: walked to the ceiling, {sequence
  // happy, randomly, stop=50, make, negation} took a second for the first pass.

  if ( $pqStop != $pqBoundsRandomly [3] ) {

    $pqRandomlyStop = $pqBoundsRandomly [3];

    if ( $pqTo == PHP_INT_MAX and ! isset ( $pqParms ['try'] ) )
      $pqTry = min ( $pqTry, $GLOBALS ['padSeqDefaultTries'] ?? 10000 );

  }

  $pqRandomly      = FALSE;
  $pqRandomlyBuild = TRUE;

?>
