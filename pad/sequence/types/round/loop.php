<?php

  if ( ! $pSeq_round )
    $pSeq_round = 1;

  return round ( $pSeq_loop / $pSeq_round ) * $pSeq_round;

?>