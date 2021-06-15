<?php

  $pad_seq_cnt++;
  $pad_seq_sts_00++;
  $pad_seq_protect_cnt++;
 
  if ( $pad_seq_random )
    $pad_seq_rand = rand ( $pad_seq_loop_idx, $pad_seq_loop_end );

  $pad_sequence = $pad_seq_loop_idx;

  if     ( $pad_pull_start )                          $pad_seq_loop_call = $pad_seq_loop_idx;
  elseif ( $pad_seq_build == 'fixed' )                $pad_seq_loop_call = $pad_seq_loop_idx;
  elseif ( isset ( $pad_seq_init [$pad_seq_cnt-1] ) ) $pad_seq_loop_call = $pad_seq_init [$pad_seq_cnt-1];
  else                                                $pad_seq_loop_call = include PAD_HOME . "sequence/types/$pad_tag/$pad_seq_build.php";    
   
  $pad_seq_base [] = $pad_seq_loop_call;

  if ( $pad_seq_build == 'jump' )
    if ( $pad_seq_loop_call !== NULL and $pad_seq_loop_call !== FALSE and $pad_seq_loop_call !== TRUE)  
      $pad_seq_loop_idx = $pad_seq_call;

  if     ( $pad_seq_loop_call === NULL)  { $pad_seq_sts_01++; return false; }
  elseif ( $pad_seq_loop_call === FALSE) { $pad_seq_sts_02++; return false; }
  elseif ( $pad_seq_loop_call === TRUE)  $pad_sequence = $pad_seq_loop_idx;
  else                                   $pad_sequence = $pad_seq_loop_call;

  if ( $pad_seq_max    and $pad_sequence > $pad_seq_max                    ) { $pad_seq_sts_04++; return false; }
  if ( $pad_seq_min    and $pad_sequence < $pad_seq_min                    ) { $pad_seq_sts_05++; return true;  }
  if ( $pad_seq_start  and count($pad_seq_base) < $pad_seq_start           ) { $pad_seq_sts_06++; return true;  }
  if ( $pad_seq_page   and count($pad_seq_base) < $pad_seq_page_start      ) { $pad_seq_sts_07++; return true;  }
  if ( $pad_seq_row    and ! in_array (count($pad_seq_base), $pad_seq_row) ) { $pad_seq_sts_08++; return true;  }
  if ( $pad_seq_value  and ! in_array ($pad_sequence, $pad_seq_value)      ) { $pad_seq_sts_09++; return true;  }
  if ( $pad_seq_checks and ! include 'check.php'                           ) { $pad_seq_sts_10++; return true;  }
  if ( $pad_seq_unique and in_array ($pad_sequence, $pad_seq_base)         ) { $pad_seq_sts_11++; return true;  }
  if ( $pad_seq_protect_cnt > $pad_seq_protect                             ) { $pad_seq_sts_12++; return false; }

  $pad_seq_protect_cnt = 0;

  if ( $pad_seq_actions )
    $pad_seq_result [] = include 'actions.php';  
  else
    $pad_seq_result [] = $pad_sequence;

  if ( $pad_seq_rows       and count($pad_seq_result) >= $pad_seq_rows         ) { $pad_seq_sts_13++; return false; }
  if ( $pad_seq_end        and count($pad_seq_base)   >= $pad_seq_end          ) { $pad_seq_sts_14++; return false; }
  if ( $pad_seq_row        and count($pad_seq_result) >= count($pad_seq_row)   ) { $pad_seq_sts_15++; return false; }
  if ( $pad_seq_value      and count($pad_seq_result) >= count($pad_seq_value) ) { $pad_seq_sts_16++; return false; }
  if ( $pad_seq_fromto_max and $pad_seq_cnt           >= $pad_seq_fromto_max   ) { $pad_seq_sts_17++; return false; }

  $pad_seq_sts_18++; 

  return true;

?>