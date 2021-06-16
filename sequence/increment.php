<?php

  if     ( $pad_seq_step     ) $pad_seq_increment = $pad_seq_step;
  elseif ( $pad_seq_multiple ) $pad_seq_increment = $pad_seq_multiple;
  else                         $pad_seq_increment = 1; 

  if ( ( $pad_seq_even or $pad_seq_odd ) and $pad_seq_increment == 1 )
    $pad_seq_increment = 2;

?>