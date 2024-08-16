<?php

  $padSeqSplice = padExplode ($padSeqActionValue, '|');

  if ( count ( $padSeqSplice ) == 1 )
    array_splice (
      $padSeqResult,
      $padSeqSplice[0]
    );
  elseif ( count ( $padSeqSplice ) == 2 )
    array_splice (
      $padSeqResult,
      $padSeqSplice[0],
      $padSeqSplice[1]
    );
  elseif ( count ( $padSeqSplice ) == 3 )
    array_splice (
      $padSeqResult,
      $padSeqSplice[0],
      $padSeqSplice[1],
      $padSeqStore [$padSeqSplice[2]]
    );

  return $padSeqResult;

?>