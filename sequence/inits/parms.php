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
  
  $padSeqRandom    = $padPrm [$pad] ['random'] ?? ''; 
  $padSeqUnique    = $padPrm [$pad] ['unique'] ?? ''; 
  $padSeqName      = $padPrm [$pad] ['name']   ?? ''; 
  $padSeqToData    = $padPrm [$pad] ['toData'] ?? ''; 
  $padSeqStoreName = $padPrm [$pad] ['store']  ?? '';
   
   if ( $padSeqMin === TRUE ) $padSeqMin = PHP_INT_MIN;
   if ( $padSeqMax === TRUE ) $padSeqMax = PHP_INT_MAX;
  
?>