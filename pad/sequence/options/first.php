<?php

  $pad_seq_first = $pad_parms_tag ['first'];

  if ( $pad_seq_first === TRUE )
    $pad_seq_first = 1;

  if ( count($pad_seq_result) <= $pad_seq_first)
    return;

  $pad_seq_result = array_slice ( $pad_seq_result, 0, $pad_seq_first );

?>