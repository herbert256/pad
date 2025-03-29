<?php

  if ( ! isset ( $padSeqStore ) )
    $padSeqStore = [];

  $padSeqFixed        = FALSE;
  $padSeqStoreUpdated = FALSE;

  $padSeqTries      = 0;
  $padSeqBase       = 0;
  $padSeqLoop       = 0;
  
  $padSeqResult     = [];
  $padSeqDone       = [];
  $padSeqInfo       = [];  
  $padSeqNames      = [];
  $padSeqPlays      = [];
  
  $padSeqPlay       = '';
  $padSeqSeq        = '';
  $padSeqPull       = '';
  $padSeqBuild      = '';

  $padSeqActionAfterName = '';

  $padSeqType   = $padType   [$pad];
  $padSeqPrefix = $padPrefix [$pad];
  $padSeqTag    = $padTag    [$pad];
  $padSeqParm   = $padOpt    [$pad] [1];

  $padTagSeq [$pad] = TRUE;
  
?>