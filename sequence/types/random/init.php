<?php

  if ( $pad_seq_prime )    return include PAD_HOME . "sequence/types/prime/random.php";
  if ( $pad_seq_power )    return include PAD_HOME . "sequence/types/power/random.php";
  if ( $pad_seq_step )     return include PAD_HOME . "sequence/types/step/random.php";
  if ( $pad_seq_multiple ) return include PAD_HOME . "sequence/types/multiple/random.php";

  return rand ( $pad_seq_min, $pad_seq_max);

?>