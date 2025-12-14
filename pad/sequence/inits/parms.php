<?php

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