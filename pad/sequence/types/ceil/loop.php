<?php

  if ( ! $padSeq_ceil )
    $padSeq_ceil = 1;

  return ceil ( $padSeq_loop / $padSeq_ceil ) * $padSeq_ceil;

?>