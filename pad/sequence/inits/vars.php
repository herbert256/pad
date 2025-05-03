<?php

  $padTagSeq [$pad] = TRUE;

  $pqFixed        = FALSE;
  $pqStoreUpdated = FALSE;
  $pqStored       = FALSE;

  $pqTries      = 0;
  $pqLoop       = 0;
  
  $pqSeq        = '';
  $pqBuild      = '';
  $pqPull       = '';
  $pqParm       = '';
  $pqAction     = '';
  $pqActionParm = '';
  $pqOrgName    = '';
  $pqOrgSet     = '';

  $pqResult     = [];
  $pqDone       = [];
  $pqInfo       = [];  
  $pqNames      = [];
  $pqPlays      = [];
  $pqPlaysHit   = [];
  $pqActions    = [];
  $pqActionsHit = [];
  $pqOrgHit     = [];
      
  $pqFrom = intval ( $padPrm [$pad] ['from']      ?? 1           );
  $pqTo   = intval ( $padPrm [$pad] ['to']        ?? PHP_INT_MAX );
 
  $pqMin  = intval ( $padPrm [$pad] ['minimal']   ?? PHP_INT_MIN );
  $pqMax  = intval ( $padPrm [$pad] ['maximal']   ?? PHP_INT_MAX );

  $pqInc  = intval ( $padPrm [$pad] ['increment'] ?? 1           );
  $pqRows = intval ( $padPrm [$pad] ['rows']      ?? 0           );
  $pqTry  = intval ( $padPrm [$pad] ['try']       ?? 0           ); 
  
  $pqStop = intval ( $padPrm [$pad] ['stop']      ?? PHP_INT_MAX );
  $pqSkip = intval ( $padPrm [$pad] ['skip']      ?? 0           );
  
  $pqRandomly  = $padPrm [$pad] ['randomly'] ?? ''; 
  $pqUnique    = $padPrm [$pad] ['unique']   ?? ''; 
  $pqName      = $padPrm [$pad] ['name']     ?? ''; 
  $pqBuildName = $padPrm [$pad] ['build']    ?? ''; 
  $pqToData    = $padPrm [$pad] ['toData']   ?? ''; 
  $pqNegative  = $padPrm [$pad] ['negative'] ?? 0 ;
  $pqPullName  = $padPrm [$pad] ['pull']     ?? '';
  $pqPush      = $padPrm [$pad] ['push']     ?? '';

  $pqType   = $padType   [$pad];
  $pqPrefix = $padPrefix [$pad];
  $pqTag    = $padTag    [$pad];

  if ( $pqPullName === TRUE ) 
    $pqPullName = $padLastPush;

      if ( pqPlay ( $pqTag  ) ) $pqPlay = $pqTag;
  elseif ( pqPlay ( $pqType ) ) $pqPlay = $pqType;
  else                                    $pqPlay = 'make';

  $pqNameGiven = $pqName;

  if ( $pqTag == 'continue' ) {
    $pqPull  = $padLastPush;
    $pqBuild = 'pull';
  }

?>