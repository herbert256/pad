<?php

  // Reads the run's named parameters and their defaults - from the tag's parameters for a tag
  // entry, from $pqSetParms for a direct one.
  //
  // Covers the range and its filters (from, to, sole, minimal, maximal, increment), the
  // limits (rows, try, stop, skip) and the behaviour flags (randomly, unique, name, build,
  // toData, negative, pull, push). Anything not given keeps a neutral default - mostly
  // PHP_INT_MAX / PHP_INT_MIN or 0 - which later stages read as "not set": inits/limits.php
  // tests $pqTo and $pqStop against PHP_INT_MAX exactly this way.

  if ( $pqEntry == 'tag')
    $pqParms = $padPrm [$pad];
  else
    $pqParms = $pqSetParms;

  $pqFrom = $pqParms ['from']      ?? 1           ;
  $pqTo   = $pqParms ['to']        ?? PHP_INT_MAX ;
  $pqSole = $pqParms ['sole']      ?? 0           ;

  $pqMin  = $pqParms ['minimal']   ?? PHP_INT_MIN ;
  $pqMax  = $pqParms ['maximal']   ?? PHP_INT_MAX ;

  $pqInc  = $pqParms ['increment'] ?? 1           ;
  $pqRows = $pqParms ['rows']      ?? 0           ;
  $pqTry  = $pqParms ['try']       ?? 0           ;

  $pqStop = $pqParms ['stop']      ?? PHP_INT_MAX ;
  $pqSkip = $pqParms ['skip']      ?? 0           ;

  $pqRandomly  = $pqParms ['randomly'] ?? '';
  $pqUnique    = $pqParms ['unique']   ?? '';
  $pqName      = $pqParms ['name']     ?? '';
  $pqBuildName = $pqParms ['build']    ?? '';
  $pqToData    = $pqParms ['toData']   ?? '';
  $pqNegative  = $pqParms ['negative'] ?? 0 ;
  $pqPull      = $pqParms ['pull']     ?? '';
  $pqPush      = $pqParms ['push']     ?? '';

?>