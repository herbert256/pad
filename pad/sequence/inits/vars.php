<?php

  $padTagSeq [$pad] = TRUE;

  $pqFixed        = FALSE;
  $pqStoreUpdated = FALSE;

  $pqTries      = 0;
  $pqLoop       = 0;
  
  $pqResult     = [];
  $pqDone       = [];
  $pqInfo       = [];  
  $pqNames      = [];
  $pqPlays      = [];
  
  $pqSeq        = '';
  $pqBuild      = '';
  $pqPull       = '';
  $pqParm       = '';
  $pqAction     = '';
  $pqActionParm = '';
 
  $pqType   = $padType   [$pad];
  $pqPrefix = $padPrefix [$pad];
  $pqTag    = $padTag    [$pad];

      if ( pqPlay ( $pqTag  ) ) $pqPlay = $pqTag;
  elseif ( pqPlay ( $pqType ) ) $pqPlay = $pqType;
  else                                    $pqPlay = 'make';
 
  $pqFrom = intval ( $padPrm [$pad] ['from']      ?? 1           );
  $pqTo   = intval ( $padPrm [$pad] ['to']        ?? PHP_INT_MAX );
 
  $pqMin  = intval ( $padPrm [$pad] ['min']       ?? PHP_INT_MIN );
  $pqMax  = intval ( $padPrm [$pad] ['max']       ?? PHP_INT_MAX );
  $pqStop = intval ( $padPrm [$pad] ['stop']      ?? PHP_INT_MAX );
  
  $pqInc  = intval ( $padPrm [$pad] ['increment'] ?? 1           );
  $pqRows = intval ( $padPrm [$pad] ['rows']      ?? 0           );
  $pqSkip = intval ( $padPrm [$pad] ['skip']      ?? 0           );
  $pqTry  = intval ( $padPrm [$pad] ['try']       ?? 10000       ); 
  
  $pqRandomly  = $padPrm [$pad] ['randomly'] ?? ''; 
  $pqUnique    = $padPrm [$pad] ['unique']   ?? ''; 
  $pqName      = $padPrm [$pad] ['name']     ?? ''; 
  $pqBuildName = $padPrm [$pad] ['build']    ?? ''; 
  $pqToData    = $padPrm [$pad] ['toData']   ?? ''; 
  $pqNegative  = $padPrm [$pad] ['negative'] ?? 0 ;
  $pqPullName  = $padPrm [$pad] ['pull']     ?? '';
  $pqPush      = $padPrm [$pad] ['push']     ?? '';

  if ( $pqPullName === TRUE ) $pqPullName = $padLastPush;

  $pqNameGiven = $pqName;

?>