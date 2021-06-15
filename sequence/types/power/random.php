<?php
  
  if (  $pad_seq_type == 'min') { 
    $pad_seq_power_min = intval ( ceil  ( log ( $pad_seq_loop_idx, $pad_seq_power ) ) );
    $pad_seq_power_max = intval ( floor ( log ( $pad_seq_loop_end, $pad_seq_power ) ) );
  }
  elseif (  $pad_seq_type == 'from') {
    $pad_seq_power_min = $pad_seq_loop_idx;
    $pad_seq_power_max = $pad_seq_loop_end;
  }
  else {
    $pad_seq_power_min = $pad_seq_loop_idx;
    $pad_seq_power_max = intval ( floor ( log ( $pad_seq_loop_end, $pad_seq_power ) ) );
  } 

  $pad_seq_work = $pad_seq_power ** rand ( $pad_seq_power_min, $pad_seq_power_max ); 

  return $pad_seq_work; 

?>