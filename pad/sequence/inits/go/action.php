<?php

  include 'sequence/inits/go/pull.php';

  $padSeqActionList = padExplode ( $padSeqParm, '|' );
  $padSeqActionParm = $padSeqActionList [0] ?? '';

  $padSeqResult = $padSeqStore [$padSeqPull];

  $padSeqInfo ["start/$padSeqInit/action"] [] = $padSeqActionName;

  include 'sequence/actions/go.php';
  
  $padSeqFixed = $padSeqResult;

  include "sequence/inits/go/start.php";

?>