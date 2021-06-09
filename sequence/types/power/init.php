<?php

  for ($pad_seq_power_idx=1; $pad_seq_power_idx <= 64; $pad_seq_power_idx++) {
    $pad_sequence = $pad_seq_power ** $pad_seq_power_idx;
    if ($pad_sequence >= $pad_seq_min)
      break;
  }

  return $pad_sequence;

?>