<?php

  $padSeqStartParms = padExplode ( $padOpt [$pad] [1], '|' );
  $padSeqStartSeq   = array_shift ( $padSeqStartParms );
  $padSeqResult     = $padSeqStore [$padSeqStartSeq];

  $padSeqActionList = $padSeqStartParms;
  $padSeqActionName = $padTag [$pad];

  include 'seq/actions/go.php';

  $padSeqStartArray = $padSeqResult;

  include 'seq/inits/start.php';

?>