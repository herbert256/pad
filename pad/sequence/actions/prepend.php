<?php

  $padSeq_prepend = pExplode ($padSeq_action_value, '|');

  foreach ( $padSeq_prepend as $padSeq_prepend_key ) {
    $padSeq_prepend_reverse = array_reverse($padSequenceStore [$padSeq_prepend_key]);
    foreach ($padSeq_prepend_reverse as $padSeq_prepend_value)
      array_unshift ($padSeq_result, $padSeq_prepend_value);
  }

  return $padSeq_result;

?>