<?php

  if ( ! $pad_seq_round )
    $pad_seq_round = 1;

  return round ( $pad_seq_loop / $pad_seq_round ) * $pad_seq_round;

?>