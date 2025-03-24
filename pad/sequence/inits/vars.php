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
  
  $padSeqPull       = '';
  $padSeqBuild      = '';

  $padSeqTag    = $padTag    [$pad];
  $padSeqType   = $padType   [$pad];
  $padSeqPrefix = $padPrefix [$pad];

  $padSeqParm1  = $padOpt [$pad] [1] ?? '';
  $padSeqParm2  = $padOpt [$pad] [2] ?? '';
  $padSeqParm3  = $padOpt [$pad] [3] ?? '';
  
?>