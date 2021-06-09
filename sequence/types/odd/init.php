<?php

  if ( $pad_seq_min % 2 )
    return $pad_seq_min;
  else
    return $pad_seq_min + 1;

?>