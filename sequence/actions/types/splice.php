<?php

  if ( count ( $padSeqActionList ) == 1 )
    array_splice (
      $padSeqResult,
      $padSeqActionList [0]
    );
  elseif ( count ( $padSeqActionList ) == 2 )
    array_splice (
      $padSeqResult,
      $padSeqActionList [0],
      $padSeqActionList [1]
    );
  elseif ( count ( $padSeqActionList ) == 3 )
    array_splice (
      $padSeqResult,
      $padSeqActionList [1],
      $padSeqActionList [2],
      $padSeqStore [$padSeqActionList [0]]
    );

  return $padSeqResult;

?>