<?php

  if ( ! $pad_seq_ceil )
    $pad_seq_ceil = 1;

  return ceil ( $pad_seq_loop / $pad_seq_ceil ) * $pad_seq_ceil;

?>