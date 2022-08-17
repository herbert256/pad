<?php

  $padSeqSplice = padExplode ($padSeqActionValue, '|');

  array_splice (
    $padSeqResult,
    $padSeqSplice[0],
    $padSeqSplice[1],
    $padSeqStore [$padSeqSplice[2] ]
  );

  return $padSeqResult;

?>