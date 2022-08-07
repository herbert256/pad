<?php

  $pSeq_random = TRUE;

  $pSeq_random_start = $pSeq_loop_start;
  $pSeq_random_end   = $pSeq_loop_end;

  if ( $pSeq_min and $pSeq_from == 1 )
    $pSeq_random_start = $pSeq_min;
  
  if ( $pSeq_max <> PHP_INT_MAX and $pSeq_to == PHP_INT_MAX )
    $pSeq_random_end = $pSeq_max;
  
  if ( $pSeq_loop_start == 1 and $pSeq_loop_end == PHP_INT_MAX and ! $pSeq_rows ) {
    $pSeq_rows = ($pSeq_max - $pSeq_min) + 1;
    $pSeq_loop_end  = $pSeq_loop_start + $pSeq_rows;
  }

?>