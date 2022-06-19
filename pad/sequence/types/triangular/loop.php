<?php

  if ( $pad_seq_now < 0 )
    return false;
 
  $pad_seq_sum = 0;

  for ($pad_seq_tri_idx = 0; $pad_seq_sum <= $pad_seq_now; $pad_seq_tri_idx++) {
    $pad_seq_sum = $pad_seq_sum + $pad_seq_tri_idx;
    if ($pad_seq_sum == $pad_seq_now)
      return true;
    elseif ($pad_seq_sum > $pad_seq_now)
      return false;
  }

  return false;
  
?>