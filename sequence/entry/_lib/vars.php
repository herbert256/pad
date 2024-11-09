<?php

  if ( ! isset ( $padSeqStore ) )
    $padSeqStore = [];

  $padSeqFixed      = FALSE;
  $padSeqTries      = 0;
  $padSeqBase       = 0;
  $padSeqLoop       = 0;
  $padSeqPull       = '';
  $padSeqResult     = [];
  $padSeqDone       = [];
  $padSeqInfo       = [];  
  $padSeqOptions    = [];
  $padSeqNoNo       = [];
  $padSeqNames      = [];
  $padSeqOperations = [];
  $padSeqSetSeq     = '';
  $padSeqSetParm    = '';
  $padSeqSetStore   = '';
  $padSeqEntryParm  = $padParm;
  $padSeqEntryList  = $padPrm [$pad];
  $padSeqParmUsed   = FALSE;
  
?>