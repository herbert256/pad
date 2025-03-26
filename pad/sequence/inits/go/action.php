<?php

  $padSeqActionList = padExplode ( $padSeqActionParms, '|' );
  $padSeqActionParm = $padSeqActionList [0] ?? '';

  $padSeqResult = $padSeqStore [$padSeqPull];

  $padSeqInfo ["start/$padStartType/action"] [] = $padSeqActionName;

  include 'sequence/actions/go.php';
  
  $padSeqFixed = $padSeqResult;

  include "sequence/inits/go/start.php";

?>