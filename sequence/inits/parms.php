<?php

  $padSeqProtect  = intval ( $padPrm [$pad] ['protect']   ?? 10000       ); 
  $padSeqSave     = intval ( $padPrm [$pad] ['save']      ?? 100         ); 

  $padSeqFrom     = intval ( $padPrm [$pad] ['from']      ?? 1           );
  $padSeqTo       = intval ( $padPrm [$pad] ['to']        ?? PHP_INT_MAX );
 
  $padSeqMin      = intval ( $padPrm [$pad] ['min']       ?? PHP_INT_MIN );
  $padSeqMax      = intval ( $padPrm [$pad] ['max']       ?? PHP_INT_MAX );
  
  $padSeqInc      = intval ( $padPrm [$pad] ['increment'] ?? 1           );
  $padSeqRows     = intval ( $padPrm [$pad] ['rows']      ?? 0           );
  $padSeqCnt      = intval ( $padPrm [$pad] ['count']     ?? 0           );
  $padSeqSkip     = intval ( $padPrm [$pad] ['skip']      ?? 0           );
  
  $padSeqRandom   =          $padPrm [$pad] ['random']    ?? ''; 
  $padSeqUnique   =          $padPrm [$pad] ['unique']    ?? ''; 
  $padSeqName     =          $padPrm [$pad] ['name']      ?? ''; 
   
  foreach ( $padPrm [$pad] as $padK => $padV )
    if ( ! isset ( $GLOBALS [ 'padSeq' . ucfirst($padK) ] ) )
      $GLOBALS [ 'padSeq' . ucfirst($padK) ] = $padV;

  $padSeqCheck = ( ! $padSeqRows and $padSeqTo == PHP_INT_MAX and $padSeqMax == PHP_INT_MAX );

?>