<?php

  $pad_seq_floor = $pad_parms_tag['floor'] ?? 1;
  if ( ! $pad_seq_floor )
    $pad_seq_floor = 1;

  return floor ( $pad_seq_action / $pad_seq_floor ) * $pad_seq_floor;

?>