<?php

  $padSeq        = 0;
  $padSeqBase    = 0;
  $padSeqResult  = $padSeqCache = [];
  $padSeqType    = "/pad/seq/types/$padSeqSeq";
  
  if ( ! $padSeqName )                           
    $padSeqName = $padSeqSet; 
  
  $padName [$pad] = $padSeqName;

  $padSeqRandom = ( isset ( $padPrm [$pad] ['random'] ) ); 
  $padSeqCheck  = ( ! $padSeqRows and $padSeqTo == PHP_INT_MAX and $padSeqMax == PHP_INT_MAX );

  $padSeqFor = FALSE;

?>