<?php

  $padSeq_inc = 2;

  $padSeq_loop_start = $padSeq_from * 2;

  if ( $padSeq_to <> PHP_INT_MAX )
    $padSeq_loop_end = $padSeq_to * 2;

  if ( $padSeq_loop_start % 2 )
    $padSeq_loop_start++;

?>