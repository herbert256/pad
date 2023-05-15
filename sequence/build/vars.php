<?php

  $padSeq         = $padSequence = $padSeqProtectCnt = $padSeqBase = 0;
  $padSeqStoreGet = '';
  $padSeqResult   = $padSeqFor = $padSeqCache = [];
  $padSeqStopNext = $padSeqStartStarted = FALSE;

  if ( ! $padSeqName )                           $padSeqName = $padSeqSet; 
  if ( ! isset($GLOBALS ["padSeq_$padSeqSeq"]) ) $GLOBALS ["padSeq_$padSeqSeq"] = $padSeqParm;
  if ( ! isset($padPrm [$pad] ["$padSeqSeq"])  ) $padPrm [$pad] ["$padSeqSeq"]  = $padSeqParm;

  if ( $padSeqSeq == 'make' )
    $padSeqFilterCheck = 'make';
  else
    $padSeqFilterCheck = 'filter';

  $padSeqSpecialOps = ['make', 'keep', 'remove'];

  $padName [$pad] = $padSeqName;

?>