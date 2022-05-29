<?php

  $pad_seq_last = $pad_parms_tag ['last'];

  if ( $pad_seq_last === TRUE )
    $pad_seq_last = 1;

  if ( count($pad_seq_result) <= $pad_seq_last)
    return;

  $pad_seq_result = array_slice ( $pad_seq_result, count($pad_seq_result)-$pad_seq_last );

?>