<?php

  if ( $padSeq_loop < 0 )
    return false;
 
  $padSeq_sum = 0;

  for ($padSeq_tri_idx = 0; $padSeq_sum <= $padSeq_loop; $padSeq_tri_idx++) {
    $padSeq_sum = $padSeq_sum + $padSeq_tri_idx;
    if ($padSeq_sum == $padSeq_loop)
      return true;
    elseif ($padSeq_sum > $padSeq_loop)
      return false;
  }

  return false;
  
?>