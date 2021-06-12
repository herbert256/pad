<?php

  if ( $pad_seq_from % 2 )
    return $pad_seq_from + 1;
  else
    return $pad_seq_from;

?>