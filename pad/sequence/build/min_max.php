<?php

  $pad_seq_minmax = ( isset($pad_parms_tag ['min']) or isset($pad_parms_tag ['max']) );

  $pad_seq_min = pad_seq_check_even_odd_up   ( $pad_seq_min, $pad_seq_even, $pad_seq_odd );
  $pad_seq_min = pad_seq_check_multiple_up   ( $pad_seq_min, $pad_seq_multiple );

  $pad_seq_max = pad_seq_check_even_odd_down ( $pad_seq_max, $pad_seq_even, $pad_seq_odd );
  $pad_seq_max = pad_seq_check_multiple_down ( $pad_seq_max, $pad_seq_multiple );

?>