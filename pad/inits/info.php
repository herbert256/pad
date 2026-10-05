<?php

  // Starts the debug/info subsystem when $padInfo asks for it, by handing over to
  // info/start/config.php - which normalises the five type flags and opens whichever of
  // track, stats, xref, trace and xml are on.
  //
  // A padReference request overrides all of that first: the reference application asks for a
  // cross-reference of a page, so only xref is wanted, the page is rendered as an include
  // (no wrappers) and tidy is switched off so the recorded output stays as the engine
  // produced it.

  // The coverage recording this request belongs to, '' for none - lib/coverage.php.

  $padCoverageRun = padCoverageRun ();

  if ( padSelfSwitch ( 'padReference' ) ) {

    $padInfo      = 'xref';
    $padInfoTrack = FALSE;
    $padInfoXml   = FALSE;
    $padInfoStats = FALSE;
    $padInfoTrace = FALSE;
    $padInfoXref  = TRUE;

    $padInclude   = TRUE;
    $padTidy      = FALSE;

  }

  // A benchmark asks a page for its timings: ?padStats, on a request this machine makes to
  // itself, turns the stats mode on for that one request, which answers them in its
  // PAD-Stats header (develop/benchmark reads them). A visitor cannot switch it.

  if ( padSelfSwitch ( 'padStats' ) ) {

    $padInfo      = $padInfo ?: 'stats';
    $padInfoStats = TRUE;

  }

  if ( $padInfo )
    include PAD . 'info/start/config.php';

?>