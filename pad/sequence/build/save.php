<?php

  if ( $padSeq_seq == 'range'   ) return;
  if ( $padSeq_rows             ) return;
  if ( $padSeq_to < PHP_INT_MAX ) return;

  if ( $padSeq_random and $padSeq_max )
    $padSeq_to = $padSeq_loop_end = $padSeq_max;

  $padSeq_rows = $padSeq_save;

?>