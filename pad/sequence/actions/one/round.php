<?php

  $pad_seq_round = $pad_parms_tag['round'] ?? 1;

  if ( ! $pad_seq_round )
    $pad_seq_round = 1;

  return round ( $pad_seq_action / $pad_seq_round ) * $pad_seq_round;

?>