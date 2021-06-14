<?php

  $pad_seq_power_min = intval ( ceil  ( log ( $pad_seq_init, $pad_seq_power ) ) );
  $pad_seq_power_max = intval ( floor ( log ( $pad_seq_exit, $pad_seq_power ) ) );

  return $pad_seq_power ** rand ( $pad_seq_power_min, $pad_seq_power_max ); 

?>