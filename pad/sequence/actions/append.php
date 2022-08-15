<?php
 
  $padSeq_append = pExplode ($padSeq_action_value, '|');

  foreach ( $padSeq_append as $padSeq_append_key )
    foreach ($padSequenceStore [$padSeq_append_key] as $padSeq_append_value)
      $padSeq_result [] = $padSeq_append_value;

  return $padSeq_result;

?>