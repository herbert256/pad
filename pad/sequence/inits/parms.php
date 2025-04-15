<?php

  $padSeqTry  = intval ( $padPrm [$pad] ['try']       ?? 10000       ); 
 
  $padSeqFrom = intval ( $padPrm [$pad] ['from']      ?? 1           );
  $padSeqTo   = intval ( $padPrm [$pad] ['to']        ?? PHP_INT_MAX );
 
  $padSeqMin  = intval ( $padPrm [$pad] ['min']       ?? PHP_INT_MIN );
  $padSeqMax  = intval ( $padPrm [$pad] ['max']       ?? PHP_INT_MAX );
  $padSeqStop = intval ( $padPrm [$pad] ['stop']      ?? PHP_INT_MAX );
  
  $padSeqInc  = intval ( $padPrm [$pad] ['increment'] ?? 1           );
  $padSeqRows = intval ( $padPrm [$pad] ['rows']      ?? 0           );
  $padSeqSkip = intval ( $padPrm [$pad] ['skip']      ?? 0           );
  
  $padSeqRandomly  = $padPrm [$pad] ['randomly'] ?? ''; 
  $padSeqUnique    = $padPrm [$pad] ['unique']   ?? ''; 
  $padSeqName      = $padPrm [$pad] ['name']     ?? ''; 
  $padSeqBuildName = $padPrm [$pad] ['build']    ?? ''; 
  $padSeqToData    = $padPrm [$pad] ['toData']   ?? ''; 
  $padSeqNegative  = $padPrm [$pad] ['negative'] ?? 0 ;
  $padSeqMake      = $padPrm [$pad] ['make']     ?? '';
  $padSeqFlag      = $padPrm [$pad] ['flag']     ?? '';
  $padSeqKeep      = $padPrm [$pad] ['keep']     ?? '';
  $padSeqRemove    = $padPrm [$pad] ['remove']   ?? '';
  $padSeqPullName  = $padPrm [$pad] ['pull']     ?? '';
  $padSeqPush      = $padPrm [$pad] ['push']     ?? '';

  $padSeqTmp    = array_keys ( $padPrm [$pad] );
  $padSeqFirst  = $padSeqTmp [0] ?? '';
  $padSeqSecond = $padSeqTmp [1] ?? '';

  $padSeqFirstParm  = ( $padSeqFirst  ) ? $padPrm [$pad] [$padSeqFirst]  : '';
  $padSeqSecondParm = ( $padSeqSecond ) ? $padPrm [$pad] [$padSeqSecond] : '';

  if ( $padSeqPullName === TRUE ) $padSeqPullName = $padLastPush;

  $padSeqNameGiven = $padSeqName;

?>