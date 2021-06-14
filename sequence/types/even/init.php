<?php

  if ( $pad_seq_init_type == 'from' )
    $pad_seq_init = intval($pad_seq_from) * 2;

  if ( $pad_seq_init % 2 )
    return $pad_seq_init + 1;
  else
    return $pad_seq_init;

?>