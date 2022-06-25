<?php

  $pad_seq_ceil = $pad_parms_tag['ceil'] ?? 1;

  if ( ! $pad_seq_ceil )
    $pad_seq_ceil = 1;

  return ceil ( $pad_seq_loop / $pad_seq_ceil ) * $pad_seq_ceil;

?>