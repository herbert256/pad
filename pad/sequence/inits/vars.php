<?php

  $padTagSeq [$pad]   = TRUE;
  $padSeqFixed        = FALSE;
  $padSeqStoreUpdated = FALSE;

  $padSeqTries      = 0;
  $padSeqBase       = 0;
  $padSeqLoop       = 0;
  
  $padSeqResult     = [];
  $padSeqDone       = [];
  $padSeqInfo       = [];  
  $padSeqNames      = [];
  $padSeqPlays      = [];
  
  $padSeqSeq        = '';
  $padSeqBuild      = '';
  $padSeqPull       = '';
  $padSeqParm       = '';
  $padSeqAction     = '';
  $padSeqActionParm = '';
 
  $padSeqType   = $padType   [$pad];
  $padSeqPrefix = $padPrefix [$pad];
  $padSeqTag    = $padTag    [$pad];

      if ( padSeqPlay ( $padSeqTag  ) ) $padSeqPlay = $padSeqTag;
  elseif ( padSeqPlay ( $padSeqType ) ) $padSeqPlay = $padSeqType;
  else                                  $padSeqPlay = 'make';
 
  $padSeqFrom = intval ( $padPrm [$pad] ['from']      ?? 1           );
  $padSeqTo   = intval ( $padPrm [$pad] ['to']        ?? PHP_INT_MAX );
 
  $padSeqMin  = intval ( $padPrm [$pad] ['min']       ?? PHP_INT_MIN );
  $padSeqMax  = intval ( $padPrm [$pad] ['max']       ?? PHP_INT_MAX );
  $padSeqStop = intval ( $padPrm [$pad] ['stop']      ?? PHP_INT_MAX );
  
  $padSeqInc  = intval ( $padPrm [$pad] ['increment'] ?? 1           );
  $padSeqRows = intval ( $padPrm [$pad] ['rows']      ?? 0           );
  $padSeqSkip = intval ( $padPrm [$pad] ['skip']      ?? 0           );
  $padSeqTry  = intval ( $padPrm [$pad] ['try']       ?? 10000       ); 
  
  $padSeqRandomly  = $padPrm [$pad] ['randomly'] ?? ''; 
  $padSeqUnique    = $padPrm [$pad] ['unique']   ?? ''; 
  $padSeqName      = $padPrm [$pad] ['name']     ?? ''; 
  $padSeqBuildName = $padPrm [$pad] ['build']    ?? ''; 
  $padSeqToData    = $padPrm [$pad] ['toData']   ?? ''; 
  $padSeqNegative  = $padPrm [$pad] ['negative'] ?? 0 ;
  $padSeqPullName  = $padPrm [$pad] ['pull']     ?? '';
  $padSeqPush      = $padPrm [$pad] ['push']     ?? '';

  if ( $padSeqPullName === TRUE ) $padSeqPullName = $padLastPush;

  $padSeqNameGiven = $padSeqName;

  $padSeqStart = $padPrm [$pad] ['from'] ?? 1; 
  $padSeqEnd   = $padPrm [$pad] ['to']   ?? PHP_INT_MAX;

?>