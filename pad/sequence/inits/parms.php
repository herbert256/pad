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
  $pqSole = $pqParms ['sole']      ?? NULL        ;

  // min= and max= are the short spellings of minimal= and maximal=: they were accepted and
  // read by nothing, so {sequence random, min=1, max=6} drew from the whole integer range.

  $pqMin  = $pqParms ['minimal']   ?? $pqParms ['min'] ?? PHP_INT_MIN ;
  $pqMax  = $pqParms ['maximal']   ?? $pqParms ['max'] ?? PHP_INT_MAX ;

  $pqInc  = $pqParms ['increment'] ?? 1           ;
  $pqRows = $pqParms ['rows']      ?? NULL        ;
  $pqTry  = $pqParms ['try']       ?? 0           ;

  $pqStop = $pqParms ['stop']      ?? PHP_INT_MAX ;
  $pqSkip = $pqParms ['skip']      ?? 0           ;

  // page= pages through the terms as it pages through the rows of a data tag, rows= being
  // the page's size (inits/limits.php, exits/page.php). It is a start option of every tag,
  // and the sequence's own handling left it to nothing: {sequence '1..10', page=2, rows=3}
  // gave 1 2 3 in silence.

  $pqPage      = ( $pqEntry == 'tag' ) ? ( $pqParms ['page'] ?? '' ) : '';

  $pqRandomly  = $pqParms ['randomly'] ?? '';
  $pqUnique    = $pqParms ['unique']   ?? '';
  $pqName      = $pqParms ['name']     ?? '';
  $pqBuildName = $pqParms ['build']    ?? '';
  $pqToData    = $pqParms ['toData']   ?? '';
  $pqNegative  = $pqParms ['negative'] ?? 0 ;
  $pqPull      = $pqParms ['pull']     ?? '';
  $pqPush      = $pqParms ['push']     ?? '';

  // A store is named by a name, not a number (pqStoreTaken): push='0' was taken for no push and
  // the terms went under 'default' in silence.

  if ( is_string ( $pqPush ) and is_numeric ( $pqPush ) ) {
    padError ( "push= takes the name of a store, not the number '$pqPush'" );
    $pqPush = '';
  }

  // Every named parameter the run consumes is read right here, so it is marked read right
  // here - the strict unread-option sweep looks for the mark.

  if ( $pqEntry == 'tag' )
    foreach ( [ 'from', 'to', 'sole', 'minimal', 'maximal', 'min', 'max', 'increment', 'rows', 'try',
                'stop', 'skip', 'randomly', 'unique', 'name', 'build', 'toData', 'page',
                'negative', 'pull', 'push' ] as $pqParmOne )
      if ( isset ( $pqParms [$pqParmOne] ) )
        padDone ( $pqParmOne );

?>
