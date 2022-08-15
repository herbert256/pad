<?php

  $padSequenceStore_get = '';
  $padSeq_result = $padSeq_for = $padSeq_cache = [];
  $padSeq = $padSequence = $padSeq_protectCnt = $padSeq_base = 0;

  if ( ! $padSeq_name )                            $padSeq_name = $padSeq_set; 
  if ( ! isset($GLOBALS ["padSeq_$padSeq_seq"]) ) $GLOBALS ["padSeq_$padSeq_seq"] = $padSeq_parm;
  if ( ! isset($padPrmsTag [$pad] ["$padSeq_seq"])   ) $padPrmsTag [$pad] ["$padSeq_seq"]   = $padSeq_parm;

  if ( $padSeq_seq == 'make' )
    $padSeq_filter_check = 'make';
  else
    $padSeq_filter_check = 'filter';

  $padSeq_special_ops = ['make', 'keep', 'remove'];

  $padName [$pad] = $padSeq_name;

?>