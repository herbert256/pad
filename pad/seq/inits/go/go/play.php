<?php

  $padSeqSeq   = $padSeqStartOperation;
  $padSeqParm  = $padSeqStartParm;
  $padSeqBuild = padSeqBuild ( $padSeqSeq, 'loop' );

  foreach ( $padSeqStore [$padSeqPull] as $padSeqStartKey => $padSeqLoop ) {

    $padSeq = include 'seq/build/call.php';

    include "seq/inits/go/$padType[$pad].php";

  }

?>