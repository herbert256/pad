<?php

  $padSeqSetName = $padSeqAction;

  include 'sequence/inits/go/pull.php';

  $padSeqActionList = padExplode ( $padSeqParm, '|' );
  $padSeqActionParm = $padSeqActionList [0] ?? '';

  $padSeqResult = $padSeqStore [$padSeqPull];

  $padSeqInfo ["start/$padSeqInitType/action"] [] = $padSeqAction;

  include 'sequence/actions/go.php';
  
  $padSeqFixed = $padSeqResult;

  include "sequence/inits/go/start.php";

?>