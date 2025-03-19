<?php

  include 'seq/inits/short/parms.php';

  $padSeqActionList = $padSeqStartParms;
  $padSeqActionName = $padTag [$pad];

  include 'seq/actions/go.php';

  $padSeqStartArray = $padSeqResult;

  include 'seq/inits/short/get.php';

?>