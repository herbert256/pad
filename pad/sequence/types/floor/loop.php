<?php  

  if ( ! $padSeq_floor )
    $padSeq_floor = 1;

  return floor ( $padSeq_loop / $padSeq_floor ) * $padSeq_floor;

?>