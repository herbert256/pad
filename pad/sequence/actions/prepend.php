<?php

  $padSeqPrepend = padExplode ($padSeqActionValue, '|');

  foreach ( $padSeqPrepend as $padSeqPrependKey ) {
    $padSeqPrependReverse = array_reverse($padSeqStore [$padSeqPrependKey]);
    foreach ($padSeqPrependReverse as $padSeqPrependValue)
      array_unshift ($padSeqResult, $padSeqPrependValue);
  }

  return $padSeqResult;

?>