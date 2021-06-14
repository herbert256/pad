<?php

  if ( $pad_seq_type == 'from' )
    $pad_seq_init = (intval($pad_seq_from) * 2) - 1;

  if ( $pad_seq_init % 2 )
    return $pad_seq_init;
  else
    return $pad_seq_init + 1;

?>