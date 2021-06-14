<?php

  if (  $pad_seq_type == 'min') { 
    $pad_seq_power_min = intval ( ceil  ( log ( $pad_seq_init, $pad_seq_power ) ) );
    $pad_seq_power_max = intval ( floor ( log ( $pad_seq_exit, $pad_seq_power ) ) );
  }
  elseif (  $pad_seq_type == 'from') {
    $pad_seq_power_min = $pad_seq_init;
    $pad_seq_power_max = $pad_seq_exit;
    $pad_seq_from      = 0;
    $pad_seq_to        = 0;
  }
  else {
    $pad_seq_power_min = $pad_seq_init;
    $pad_seq_power_max = intval ( floor ( log ( $pad_seq_exit, $pad_seq_power ) ) );
  } 

  $pad_seq_work = $pad_seq_power ** rand ( $pad_seq_power_min, $pad_seq_power_max ); 

  return $pad_seq_work; 

?>