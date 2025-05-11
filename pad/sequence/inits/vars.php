<?php

  $padTagSeq [$pad] = TRUE;

  $pqFixed        = FALSE;
  $pqStoreUpdated = FALSE;
  $pqStored       = FALSE;
  $pqInPlays      = FALSE;

  $pqTries      = 0;
  $pqLoop       = 0;
  
  $pqSeq        = '';
  $pqBuild      = '';
  $pqParm       = '';
  $pqAction     = '';
  $pqActionParm = '';
  $pqOrgName    = '';
  $pqOrgSet     = '';

  $pqResult     = [];
  $pqDone       = [];
  $pqInfo       = [];  
  $pqNames      = [];
  $pqActions    = [];
  $pqPlays      = [];
  $pqPlaysHit   = [];
  $pqActions    = [];
  $pqActionsHit = [];
  $pqOrgHit     = [];
      
  $pqFrom = $padPrm [$pad] ['from']      ?? 1           ;
  $pqTo   = $padPrm [$pad] ['to']        ?? PHP_INT_MAX ;
  $pqSole = $padPrm [$pad] ['sole']      ?? 0           ;
 
  if ( $pqSole ) 
    $pqFrom = $pqTo = $pqSole;

  $pqMin  = $padPrm [$pad] ['minimal']   ?? PHP_INT_MIN ;
  $pqMax  = $padPrm [$pad] ['maximal']   ?? PHP_INT_MAX ;

  $pqInc  = $padPrm [$pad] ['increment'] ?? 1           ;
  $pqRows = $padPrm [$pad] ['rows']      ?? 0           ;
  $pqTry  = $padPrm [$pad] ['try']       ?? 0           ; 
  
  $pqStop = $padPrm [$pad] ['stop']      ?? PHP_INT_MAX ;
  $pqSkip = $padPrm [$pad] ['skip']      ?? 0           ;
  
  $pqRandomly  = $padPrm [$pad] ['randomly'] ?? ''; 
  $pqUnique    = $padPrm [$pad] ['unique']   ?? ''; 
  $pqName      = $padPrm [$pad] ['name']     ?? ''; 
  $pqBuildName = $padPrm [$pad] ['build']    ?? ''; 
  $pqToData    = $padPrm [$pad] ['toData']   ?? ''; 
  $pqNegative  = $padPrm [$pad] ['negative'] ?? 0 ;
  $pqPull      = $padPrm [$pad] ['pull']     ?? '';
  $pqPush      = $padPrm [$pad] ['push']     ?? '';
  $pqOne       = $padPrm [$pad] ['one']      ?? '';

  $pqType   = $padType   [$pad];
  $pqPrefix = $padPrefix [$pad];
  $pqTag    = $padTag    [$pad];

  if ( $pqPull === TRUE ) 
    $pqPull = $padLastPush;

      if ( pqPlay ( $pqTag  ) ) $pqPlay = $pqTag;
  elseif ( pqPlay ( $pqType ) ) $pqPlay = $pqType;
  else                          $pqPlay = 'make';

  $pqNameGiven = $pqName;

  if ( $pqTag == 'continue' ) {
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

?>