<?php
 
  $pSeq_append = pExplode ($pSeq_action_value, '|');

  foreach ( $pSeq_append as $pSeq_append_key )
    foreach ($pSequence_store [$pSeq_append_key] as $pSeq_append_value)
      $pSeq_result [] = $pSeq_append_value;

  return $pSeq_result;

?>