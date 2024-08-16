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
  
  $padSeqUnique   =          $padPrm [$pad] ['unique']    ?? '';
  $padSeqPush     =          $padPrm [$pad] ['store']     ?? ''; 
  $padSeqPull     =          $padPrm [$pad] ['sequence']  ?? '';
  $padSeqRange    =          $padPrm [$pad] ['range']     ?? '';
  $padSeqList     =          $padPrm [$pad] ['list']      ?? '';

  $padSeqName     =          $padName [$pad]              ?? ''; 
   
  if ( ! $padSeqPull and $padOpt[$pad][1] and isset ( $padSeqStore [$padOpt[$pad][1]] ) )
    $padSeqPull = $padOpt[$pad][1] ;

  if ( ! $padSeqRange and strpos ( $padOpt[$pad][1], '..') )
    $padSeqRange = $padOpt[$pad][1] ;
  
  if ( ! $padSeqList and strpos ( $padOpt[$pad][1], ';') )
    $padSeqList = $padOpt[$pad][1] ;

  foreach ( $padPrm [$pad] as $padSeqTagName => $padSeqTagValue )
    if ( ! isset ( $GLOBALS [ 'padSeq' . ucfirst($padSeqTagName) ] ) )
      $GLOBALS [ 'padSeq' . ucfirst($padSeqTagName) ] = $padSeqTagValue;

?>