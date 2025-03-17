<?php

  $padSeqStartSeq = $padOpt [$pad] [1];      
  $padSeqResult   = $padSeqStore [$padSeqStartSeq];
  $padPrmName     = $padTag [$pad];
  $padPrmValue    = $padOpt [$pad] [2] ?? ''; 

  include "seq/one/one.php";

  $padSeqStartArray = $padSeqResult;

  include 'seq/inits/start.php';

?>