<?php

  $pad_seq_power_min = ceil  ( log ( $pad_seq_min, $pad_seq_power ) );
  $pad_seq_power_max = floor ( log ( $pad_seq_max, $pad_seq_power ) );

  return $pad_seq_power ** rand ( $pad_seq_power_min, $pad_seq_power_max ); 

?>