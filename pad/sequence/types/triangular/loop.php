<?php

  if ( $pad_seq_loop < 0 )
    return false;
 
  $pad_seq_sum = 0;

  for ($pad_seq_tri_idx = 0; $pad_seq_sum <= $pad_seq_loop; $pad_seq_tri_idx++) {
    $pad_seq_sum = $pad_seq_sum + $pad_seq_tri_idx;
    if ($pad_seq_sum == $pad_seq_loop)
      return true;
    elseif ($pad_seq_sum > $pad_seq_loop)
      return false;
  }

  return false;
  
?>