<?php
  
  if (  $pad_seq_type == 'from') {
    $pad_seq_power_min = $pad_seq_init;
    $pad_seq_power_max = $pad_seq_exit;
  }
  else {
    $pad_seq_power_min = intval ( ceil  ( log ( $pad_seq_init, $pad_seq_parm ) ) );
    $pad_seq_power_max = intval ( floor ( log ( $pad_seq_exit, $pad_seq_parm ) ) );
  } 

  $pad_seq_work = $pad_seq_parm ** rand ( $pad_seq_power_min, $pad_seq_power_max ); 

  return $pad_seq_work; 

?>