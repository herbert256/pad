<?php

  if ( $pad_sequence < 0 )
    return false;
 
  $pad_seq_sum = 0;

  for ($pad_seq_tri_idx = 0; $pad_seq_sum <= $pad_sequence; $pad_seq_tri_idx++) {
    $pad_seq_sum = $pad_seq_sum + $pad_seq_tri_idx;
    if ($pad_seq_sum == $pad_sequence)
      return true;
    elseif ($pad_seq_sum > $pad_sequence)
      return false;
  }

  return false;
  
?>