<?php

  if ( ! $pSeq_ceil )
    $pSeq_ceil = 1;

  return ceil ( $pSeq_loop / $pSeq_ceil ) * $pSeq_ceil;

?>