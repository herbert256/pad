<?php

  $padSeqStoreGet = '';
  $padSeqResult = $padSeqFor = $padSeqCache = [];
  $padSeq = $padSequence = $padSeqProtectCnt = $padSeqBase = 0;

  if ( ! $padSeqName )                            $padSeqName = $padSeqSet; 
  if ( ! isset($GLOBALS ["padSeq_$padSeqSeq"]) ) $GLOBALS ["padSeq_$padSeqSeq"] = $padSeqParm;
  if ( ! isset($padPrmsTag [$pad] ["$padSeqSeq"])   ) $padPrmsTag [$pad] ["$padSeqSeq"]   = $padSeqParm;

  if ( $padSeqSeq == 'make' )
    $padSeqFilterCheck = 'make';
  else
    $padSeqFilterCheck = 'filter';

  $padSeqSpecialOps = ['make', 'keep', 'remove'];

  $padName [$pad] = $padSeqName;

?>