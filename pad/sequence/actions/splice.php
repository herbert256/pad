<?php

  $pSeq_splice = pExplode ($pSeq_action_value, '|');

  array_splice (
    $pSeq_result,
    $pSeq_splice[0],
    $pSeq_splice[1],
    $pSequenceStore [$pSeq_splice[2] ]
  );

  return $pSeq_result;

?>