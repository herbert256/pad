<?php

  $pad_seq_splice = pad_explode ($pad_seq_action_value, '|');

  array_splice (
    $pad_seq_result,
    $pad_seq_splice[0],
    $pad_seq_splice[1],
    $pad_seq_store [$pad_seq_splice[2] ]
  );

  return $pad_seq_result;

?>