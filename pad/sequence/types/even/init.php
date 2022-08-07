<?php

  $pSeq_inc = 2;

  $pSeq_loop_start = $pSeq_from * 2;

  if ( $pSeq_to <> PHP_INT_MAX )
    $pSeq_loop_end = $pSeq_to * 2;

  if ( $pSeq_loop_start % 2 )
    $pSeq_loop_start++;

?>