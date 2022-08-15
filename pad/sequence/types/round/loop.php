<?php

  if ( ! $padSeq_round )
    $padSeq_round = 1;

  return round ( $padSeq_loop / $padSeq_round ) * $padSeq_round;

?>