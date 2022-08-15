<?php

  $padSeq_random = TRUE;

  $padSeq_random_start = $padSeq_loop_start;
  $padSeq_random_end   = $padSeq_loop_end;

  if ( $padSeq_min and $padSeq_from == 1 )
    $padSeq_random_start = $padSeq_min;
  
  if ( $padSeq_max <> PHP_INT_MAX and $padSeq_to == PHP_INT_MAX )
    $padSeq_random_end = $padSeq_max;
  
  if ( $padSeq_loop_start == 1 and $padSeq_loop_end == PHP_INT_MAX and ! $padSeq_rows ) {
    $padSeq_rows = ($padSeq_max - $padSeq_min) + 1;
    $padSeq_loop_end  = $padSeq_loop_start + $padSeq_rows;
  }

?>