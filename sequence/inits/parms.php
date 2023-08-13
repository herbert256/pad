<?php

  $padSeqProtect  =          $padPrm [$pad] ['protect']   ?? 10000; 
  $padSeqSave     =          $padPrm [$pad] ['save']      ?? 100; 

  $padSeqFrom     = intval ( $padPrm [$pad] ['from']      ?? 1           );
  $padSeqTo       = intval ( $padPrm [$pad] ['to']        ?? PHP_INT_MAX );
 
  $padSeqMin      = intval ( $padPrm [$pad] ['min']       ?? PHP_INT_MIN );
  $padSeqMax      = intval ( $padPrm [$pad] ['max']       ?? PHP_INT_MAX );
  
  $padSeqInc      = intval ( $padPrm [$pad] ['increment'] ?? 1           );
  $padSeqRows     = intval ( $padPrm [$pad] ['rows']      ?? 0           );
  $padSeqUnique   = intval ( $padPrm [$pad] ['unique']    ?? 0           );
  $padSeqCnt      = intval ( $padPrm [$pad] ['count']     ?? 0           );
  $padSeqPage     = intval ( $padPrm [$pad] ['page']      ?? 0           );
  
  $padSeqUnique   =          $padPrm [$pad] ['unique']    ?? '';
  $padSeqPush     =          $padPrm [$pad] ['store']     ?? ''; 
  $padSeqPull     =          $padPrm [$pad] ['sequence']  ?? '';
  $padSeqRange    =          $padPrm [$pad] ['range']     ?? '';
  $padSeqList     =          $padPrm [$pad] ['list']      ?? '';
  $padSeqKeep     =          $padPrm [$pad] ['keep']      ?? '';
  $padSeqRemove   =          $padPrm [$pad] ['remove']    ?? '';
  $padSeqMake     =          $padPrm [$pad] ['make']      ?? '';
  $padSeqUpdate   =          $padPrm [$pad] ['update']    ?? '';

  $padSeqName     =          $padName [$pad]              ?? ''; 
  
  unset ( $padPrm [$pad] ['store'] );
 
  if ( ! $padSeqPull and isset ( $padSeqStore [$padOpt[$pad][1]] ) )
    $padSeqPull = $padOpt[$pad][1] ;

  foreach ( $padPrm [$pad] as $padSeqTagName => $padSeqTagValue )
    if ( ! isset ( $GLOBALS [ 'padSeq' . ucfirst($padSeqTagName) ] ) )
      $GLOBALS [ 'padSeq' . ucfirst($padSeqTagName) ] = $padSeqTagValue;

?>