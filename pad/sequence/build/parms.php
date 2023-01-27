<?php

  $padSeqFrom     = intval ( $padPrmsTag [$pad] ['from']      ?? 1           );
  $padSeqTo       = intval ( $padPrmsTag [$pad] ['to']        ?? PHP_INT_MAX );
  
  $padSeqMin      = intval ( $padPrmsTag [$pad] ['min']       ?? 0           );
  $padSeqMax      = intval ( $padPrmsTag [$pad] ['max']       ?? PHP_INT_MAX );
  
  $padSeqStart    = intval ( $padPrmsTag [$pad] ['start']     ?? 0           );
  $padSeqEnd      = intval ( $padPrmsTag [$pad] ['end']       ?? PHP_INT_MAX );
  
  $padSeqLow      = intval ( $padPrmsTag [$pad] ['low']       ?? 0           );
  $padSeqHigh     = intval ( $padPrmsTag [$pad] ['high']      ?? PHP_INT_MAX );

  $padSeqInc      = intval ( $padPrmsTag [$pad] ['increment'] ?? 1           );
  $padSeqRows     = intval ( $padPrmsTag [$pad] ['rows']      ?? 0           );
  $padSeqUnique   = intval ( $padPrmsTag [$pad] ['unique']    ?? 0           );
  $padSeqRandom   = intval ( $padPrmsTag [$pad] ['random']    ?? 0           );
  $padSeqCnt      = intval ( $padPrmsTag [$pad] ['count']     ?? 0           );
  $padSeqPage     = intval ( $padPrmsTag [$pad] ['page']      ?? 0           );
  
  $padSeqProtect  =          $padPrmsTag [$pad] ['protect']   ?? 1000; 
  $padSeqSave     =          $padPrmsTag [$pad] ['save']      ?? 100; 
  $padSeqUnique   =          $padPrmsTag [$pad] ['unique']    ?? '';
  $padSeqPush     =          $padPrmsTag [$pad] ['store']     ?? ''; 
  $padSeqPull     =          $padPrmsTag [$pad] ['sequence']  ?? '';
  $padSeqRange    =          $padPrmsTag [$pad] ['range']     ?? '';
  $padSeqKeep     =          $padPrmsTag [$pad] ['keep']      ?? '';
  $padSeqRemove   =          $padPrmsTag [$pad] ['remove']    ?? '';
  $padSeqMake     =          $padPrmsTag [$pad] ['make']      ?? '';
  $padSeqUpdate   =          $padPrmsTag [$pad] ['update']    ?? '';

  $padSeqName     =          $padName    [$pad]               ?? ''; 
  
  unset ( $padPrmsTag [$pad] ['store'] );
 
  foreach ( $padPrmsTag [$pad] as $padSeqTagName => $padSeqTagValue )
    if ( ! isset ( $GLOBALS [ 'padSeq' . ucfirst($padSeqTagName) ] ) )
      $GLOBALS [ 'padSeq' . ucfirst($padSeqTagName) ] = $padSeqTagValue;

?>