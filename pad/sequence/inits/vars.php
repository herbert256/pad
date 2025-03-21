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
  $padSeqOptions    = [];
  $padSeqNames      = [];
  $padSeqPlays      = [];
  
  $padSeqPull       = '';
  $padSeqBuild      = '';

?>