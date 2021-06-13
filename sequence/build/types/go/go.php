<?php

  $pad_seq_protect_cnt++;

  if ( is_null($pad_sequence)                                      ) return false;
  if ( $pad_seq_max and $pad_sequence > $pad_seq_max               ) return false;
  if ( $pad_seq_protect_cnt > $pad_seq_protect                     ) return false;
  if ( $pad_seq_unique and in_array ($pad_sequence, $pad_seq_base) ) return true; 

  $pad_seq_base_cnt++;
  $pad_seq_base [$pad_seq_base_cnt] = $pad_sequence;

  if ( $pad_sequence === FALSE                                              ) return true;
  if ( $pad_seq_min    and $pad_sequence < $pad_seq_min                     ) return true;
  if ( $pad_seq_start  and count($pad_seq_base) < $pad_seq_start            ) return true;
  if ( $pad_seq_page   and count($pad_seq_base) < $pad_seq_page_start       ) return true;
  if ( $pad_seq_row_   and ! in_array (count($pad_seq_base), $pad_seq_rows) ) return true;
  if ( $pad_seq_value  and ! in_array ($pad_sequence, $pad_seq_value)       ) return true;
  if ( $pad_seq_checks and ! include PAD_HOME . "sequence/build/check.php"  ) return true;     

  if ( $pad_seq_actions )
    $pad_seq_result [] = include PAD_HOME . "sequence/build/actions.php";  
  else
    $pad_seq_result [] = $pad_sequence;  

  $pad_seq_protect_cnt = 0;

  if ( $pad_seq_rows     and $pad_seq_rows            == count($pad_seq_result) ) return false;
  if ( $pad_seq_end  and $pad_seq_end                 == count($pad_seq_base)   ) return false;
  if ( $pad_seq_row_rows and count($pad_seq_row_rows) == count($pad_seq_result) ) return false;
  if ( $pad_seq_value    and count($pad_seq_value)    == count($pad_seq_result) ) return false;

  return true;

?>