<?php

  if ( $pad_seq_init_type == 'from' )
    $pad_seq_init = intval($pad_seq_from) * $pad_seq_multiple;

  return ceil ( $pad_seq_init / $pad_seq_multiple ) * $pad_seq_multiple;

?>