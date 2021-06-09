<?php

  if ( $pad_seq_check < 0 )
    return false;
 
  $pad_seq_sum = 0;

  for ($pad_seq_idx = 0; $pad_seq_sum <= $pad_seq_check; $pad_seq_idx++) {
    $pad_seq_sum = $pad_seq_sum + $pad_seq_idx;
    if ($pad_seq_sum == $pad_seq_check)
      return true;
    elseif ($pad_seq_sum > $pad_seq_check)
      return false;
  }

  return false;
  
?>