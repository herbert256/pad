<?php

  $pad_seq_perfect = [];

  if ($pad_seq_min == 0)
    $pad_seq_min = 1; 

  for ($pad_seq_perfect_check = $pad_seq_min; $pad_seq_perfect_check <= $pad_seq_max; $pad_seq_perfect_check++) {

    $pad_seq_perfect_sum = 0; 
    
    for ($pad_seq_i = 1; $pad_seq_i < $pad_seq_perfect_check; $pad_seq_i++) 
      if ($pad_seq_perfect_check % $pad_seq_i == 0) 
        $pad_seq_perfect_sum = $pad_seq_perfect_sum + $pad_seq_i; 
    
    if ($pad_seq_perfect_sum == $pad_seq_perfect_check)
      $pad_seq_perfect [] = $pad_seq_perfect_check; 
  
  }

  return $pad_seq_perfect;

?>