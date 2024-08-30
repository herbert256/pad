<?php

  $padSeqTry    = intval ( $padPrm [$pad] ['try']       ?? 10000       ); 
 
  $padSeqFrom   = intval ( $padPrm [$pad] ['from']      ?? 1           );
  $padSeqTo     = intval ( $padPrm [$pad] ['to']        ?? PHP_INT_MAX );
 
  $padSeqMin    = intval ( $padPrm [$pad] ['min']       ?? PHP_INT_MIN );
  $padSeqMax    = intval ( $padPrm [$pad] ['max']       ?? PHP_INT_MAX );
  
  $padSeqInc    = intval ( $padPrm [$pad] ['increment'] ?? 1           );
  $padSeqRows   = intval ( $padPrm [$pad] ['rows']      ?? 0           );
  $padSeqSkip   = intval ( $padPrm [$pad] ['skip']      ?? 0           );
  
  $padSeqRandom = $padPrm [$pad] ['random'] ?? ''; 
  $padSeqUnique = $padPrm [$pad] ['unique'] ?? ''; 
  $padSeqName   = $padPrm [$pad] ['name']   ?? ''; 
  $padSeqBuild  = $padPrm [$pad] ['build']  ?? ''; 
  $padSeqToData = $padPrm [$pad] ['toData'] ?? ''; 
   
  foreach ( $padPrm [$pad] as $padK => $padV )
    if ( ! isset ( $GLOBALS [ padSeqName ($padK) ] ) )
      $GLOBALS [ padSeqName ($padK) ] = $padV;

?>