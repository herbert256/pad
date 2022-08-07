<?php

  $pSeq_prepend = pExplode ($pSeq_action_value, '|');

  foreach ( $pSeq_prepend as $pSeq_prepend_key ) {
    $pSeq_prepend_reverse = array_reverse($pSequence_store [$pSeq_prepend_key]);
    foreach ($pSeq_prepend_reverse as $pSeq_prepend_value)
      array_unshift ($pSeq_result, $pSeq_prepend_value);
  }

  return $pSeq_result;

?>