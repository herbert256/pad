<?php

  $padSeqFrom     = intval ( $padPrm [$pad] ['from']      ?? 1           );
  $padSeqTo       = intval ( $padPrm [$pad] ['to']        ?? PHP_INT_MAX );
  
  $padSeqMin      = intval ( $padPrm [$pad] ['min']       ?? 0           );
  $padSeqMax      = intval ( $padPrm [$pad] ['max']       ?? PHP_INT_MAX );
  
  $padSeqStart    = intval ( $padPrm [$pad] ['start']     ?? 0           );
  $padSeqEnd      = intval ( $padPrm [$pad] ['end']       ?? PHP_INT_MAX );
  
  $padSeqLow      = intval ( $padPrm [$pad] ['low']       ?? 0           );
  $padSeqHigh     = intval ( $padPrm [$pad] ['high']      ?? PHP_INT_MAX );

  $padSeqInc      = intval ( $padPrm [$pad] ['increment'] ?? 1           );
  $padSeqRows     = intval ( $padPrm [$pad] ['rows']      ?? 0           );
  $padSeqUnique   = intval ( $padPrm [$pad] ['unique']    ?? 0           );
  $padSeqRandom   = intval ( $padPrm [$pad] ['random']    ?? 0           );
  $padSeqCnt      = intval ( $padPrm [$pad] ['count']     ?? 0           );
  $padSeqPage     = intval ( $padPrm [$pad] ['page']      ?? 0           );
  
  $padSeqProtect  =          $padPrm [$pad] ['protect']   ?? 10000; 
  $padSeqSave     =          $padPrm [$pad] ['save']      ?? 100; 
  $padSeqUnique   =          $padPrm [$pad] ['unique']    ?? '';
  $padSeqPush     =          $padPrm [$pad] ['store']     ?? ''; 
  $padSeqPull     =          $padPrm [$pad] ['sequence']  ?? '';
  $padSeqRange    =          $padPrm [$pad] ['range']     ?? '';
  $padSeqKeep     =          $padPrm [$pad] ['keep']      ?? '';
  $padSeqRemove   =          $padPrm [$pad] ['remove']    ?? '';
  $padSeqMake     =          $padPrm [$pad] ['make']      ?? '';
  $padSeqUpdate   =          $padPrm [$pad] ['update']    ?? '';

  $padSeqName     =          $padName [$pad]              ?? ''; 
  
  unset ( $padPrm [$pad] ['store'] );
 
  foreach ( $padPrm [$pad] as $padSeqTagName => $padSeqTagValue )
    if ( ! isset ( $GLOBALS [ 'padSeq' . ucfirst($padSeqTagName) ] ) )
      $GLOBALS [ 'padSeq' . ucfirst($padSeqTagName) ] = $padSeqTagValue;

?>