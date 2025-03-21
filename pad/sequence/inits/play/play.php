<?php 

  $padSeqSeq   = $padSeqPlayOperation;
  $padSeqParm  = $padSeqPlayParm;
  $padSeqBuild = padSeqBuild ( $padSeqSeq, 'loop' );

  $padSeqFixed = [];

  foreach ( $padSeqStore [$padSeqPull] as $padSeqPlayKey => $padSeqLoop ) {

    $padSeq = include 'sequence/build/call.php';

    include "sequence/inits/play/$padPlayAction.php";

  }

  include "sequence/inits/go/start.php";

?>