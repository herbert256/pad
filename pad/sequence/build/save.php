<?php

  if ( $pSeq_seq == 'range'   ) return;
  if ( $pSeq_rows             ) return;
  if ( $pSeq_to < PHP_INT_MAX ) return;

  if ( $pSeq_random and $pSeq_max )
    $pSeq_to = $pSeq_loop_end = $pSeq_max;

  $pSeq_rows = $pSeq_save;

?>