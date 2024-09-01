<?php

  if ( ! isset ( $padSeqStore ) )
    $padSeqStore = [];

  $padSeqResult = [];
  $padSeqDone   = [];
  $padSeqFixed  = FALSE;
  $padSeqTries  = 0;
  $padSeqBase   = 0;
  $padSeqLoop   = 0;
  $padSeqPull   = '';

?>