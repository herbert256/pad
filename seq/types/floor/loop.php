<?php  

  if ( ! $padSeqFloor )
    $padSeqFloor = 1;

  return floor ( $padSeqLoop / $padSeqFloor ) * $padSeqFloor;

?>