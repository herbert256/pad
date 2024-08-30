<?php  

  if ( ! $padSeqParm )
    $padSeqParm = 1;

  return floor ( $padSeqLoop / $padSeqParm ) * $padSeqParm;

?>