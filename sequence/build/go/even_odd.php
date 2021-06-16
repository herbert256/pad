<?php

  if ( $pad_seq_even and  $pad_seq_add % 2 )
    $pad_seq_add++;

  if ( $pad_seq_odd and ! $pad_seq_add % 2 )
    $pad_seq_add++;

  if ( $pad_seq > $pad_seq_max)
    $pad_seq_add = $pad_seq_add - 2;

?>