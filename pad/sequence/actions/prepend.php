<?php

  $pad_seq_prepend = pad_explode ($pad_seq_action_value, '|');

  foreach ( $pad_seq_prepend as $pad_seq_prepend_key ) {
    $pad_seq_prepend_reverse = array_reverse($pad_seq_store [$pad_seq_prepend_key]);
    foreach ($pad_seq_prepend_reverse as $pad_seq_prepend_value)
      array_unshift ($pad_seq_result, $pad_seq_prepend_value);
  }

  return $pad_seq_result;

?>