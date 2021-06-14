<?php

  if ( $pad_seq_init_type == 'from' )
    $pad_seq_init = intval($pad_seq_from) * $pad_seq_step;

  return $pad_seq_init;

?>