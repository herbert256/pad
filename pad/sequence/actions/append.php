<?php

  $pad_seq_append = pad_explode ($pad_seq_action_value, '|');

  foreach ( $pad_seq_append as $pad_seq_append_key )
    foreach ($pad_seq_store [$pad_seq_append_key] as $pad_seq_append_value)
      $pad_seq_result [] = $pad_seq_append_value;

  return $pad_seq_result;

?>