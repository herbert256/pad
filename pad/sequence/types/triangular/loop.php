<?php

  if ( $pSeq_loop < 0 )
    return false;
 
  $pSeq_sum = 0;

  for ($pSeq_tri_idx = 0; $pSeq_sum <= $pSeq_loop; $pSeq_tri_idx++) {
    $pSeq_sum = $pSeq_sum + $pSeq_tri_idx;
    if ($pSeq_sum == $pSeq_loop)
      return true;
    elseif ($pSeq_sum > $pSeq_loop)
      return false;
  }

  return false;
  
?>