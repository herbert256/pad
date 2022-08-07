<?php  

  if ( ! $pSeq_floor )
    $pSeq_floor = 1;

  return floor ( $pSeq_loop / $pSeq_floor ) * $pSeq_floor;

?>