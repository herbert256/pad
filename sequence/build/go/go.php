<?php

  $pad_seq_sts_00++;

  $pad_seq_cnt++;
  $pad_seq_fromto_cnt++;
  $pad_seq_protect_cnt++;

  if ( is_null($pad_sequence)                                      ) { $pad_seq_sts_01++; return false; }
  if ( $pad_seq_to  and $pad_seq_fromto_cnt > $pad_seq_to          ) { $pad_seq_sts_02++; return false; }
  if ( $pad_seq_max and $pad_sequence > $pad_seq_max               ) { $pad_seq_sts_03++; return false; }
  if ( $pad_seq_protect_cnt > $pad_seq_protect                     ) { $pad_seq_sts_04++; return false; }
  if ( $pad_seq_unique and in_array ($pad_sequence, $pad_seq_base) ) { $pad_seq_sts_05++; return true;  } 

  $pad_seq_base_cnt++;
  $pad_seq_base [$pad_seq_base_cnt] = $pad_sequence;

  if ( $pad_sequence === FALSE                                          ) { $pad_seq_sts_06++; return true; }
  if ( $pad_seq_min    and $pad_sequence < $pad_seq_min                 ) { $pad_seq_sts_07++; return true; }
  if ( $pad_seq_start  and count($pad_seq_base) < $pad_seq_start        ) { $pad_seq_sts_08++; return true; }
  if ( $pad_seq_page   and $pad_seq_base_cnt < $pad_seq_page_start      ) { $pad_seq_sts_09++; return true; }
  if ( $pad_seq_row    and ! in_array ($pad_seq_base_cnt, $pad_seq_row) ) { $pad_seq_sts_10++; return true; }
  if ( $pad_seq_value  and ! in_array ($pad_sequence, $pad_seq_value)   ) { $pad_seq_sts_11++; return true; }
  if ( $pad_seq_checks and ! include 'check.php'                        ) { $pad_seq_sts_12++; return true; }

  $pad_seq_protect_cnt = 0;
  $pad_seq_result_cnt++;

  if ( $pad_seq_actions )
    $pad_seq_result [$pad_seq_result_cnt] = include 'actions.php';  
  else
    $pad_seq_result [$pad_seq_result_cnt] = $pad_sequence;

  if ( $pad_seq_rows       and $pad_seq_result_cnt >= $pad_seq_rows         ) { $pad_seq_sts_13++; return false; }
  if ( $pad_seq_end        and $pad_seq_base_cnt   >= $pad_seq_end          ) { $pad_seq_sts_14++; return false; }
  if ( $pad_seq_row        and $pad_seq_result_cnt >= count($pad_seq_row)   ) { $pad_seq_sts_15++; return false; }
  if ( $pad_seq_value      and $pad_seq_result_cnt >= count($pad_seq_value) ) { $pad_seq_sts_16++; return false; }
  if ( $pad_seq_fromto_max and $pad_seq_cnt        >= $pad_seq_fromto_max   ) { $pad_seq_sts_17++; return false; }

  $pad_seq_sts_18++; 

  return true;

?>