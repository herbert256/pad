<?php

  $pad_seq_range_parms = pad_explode ( $pad_seq_range, '..' );

  if ( is_numeric ( $pad_seq_range_parms[0] ) ) {

    $pad_seq_from = $pad_seq_range_parms[0];
    $pad_seq_to   = $pad_seq_range_parms[1];

    $pad_seq_fromto_cnt = $pad_seq_from - 1;

    $pad_seq_type = 'from';

  } else {

    $pad_seq_type = '';

    $pad_seq_from = 0;
    $pad_seq_to   = 0;
    $pad_seq_min  = 0;
    $pad_seq_max  = 0;

  }

  return range ( $pad_seq_range_parms[0], $pad_seq_range_parms[1], $pad_parms_tag['step'] ?? 1 );

?>