<?php

  $pSequenceStore_get = '';
  $pSeq_result = $pSeq_for = $pSeq_cache = [];
  $pSeq = $pSequence = $pSeq_protectCnt = $pSeq_base = 0;

  if ( ! $pSeq_name )                            $pSeq_name = $pSeq_set; 
  if ( ! isset($GLOBALS ["pSeq_$pSeq_seq"]) ) $GLOBALS ["pSeq_$pSeq_seq"] = $pSeq_parm;
  if ( ! isset($pPrmsTag[$p] ["$pSeq_seq"])   ) $pPrmsTag[$p] ["$pSeq_seq"]   = $pSeq_parm;

  if ( $pSeq_seq == 'make' )
    $pSeq_filter_check = 'make';
  else
    $pSeq_filter_check = 'filter';

  $pSeq_special_ops = ['make', 'keep', 'remove'];

  $pName[$p] = $pName[$p] = $pSeq_name;

?>