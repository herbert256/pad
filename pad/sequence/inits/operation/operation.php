<?php

  $padSeqOperationParms = padExplode ( $padSeqOperationParms, '|' );
  $padSeqOperationParm  = $padSeqOperationParms [0] ?? '';

  $padSeqResult = $padSeqStore [$padSeqPull];

  include "sequence/inits/operation/$padSeqOperationType.php";

  $padSeqFixed = $padSeqResult;

  include "sequence/inits/go/start.php";

?>