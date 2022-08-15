<?php

  $padSeq_splice = padExplode ($padSeq_action_value, '|');

  array_splice (
    $padSeq_result,
    $padSeq_splice[0],
    $padSeq_splice[1],
    $padSequenceStore [$padSeq_splice[2] ]
  );

  return $padSeq_result;

?>