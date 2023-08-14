<?php
  
  if ( gmp_prob_prime ($padSeqLoop) )
    $padSeqWalk = $padSeqLoop;
  else
    $padSeqWalk = intval ( gmp_nextprime ($padSeqLoop) );

  $padSeqLoop = $padSeqWalk + $padSeqInc;
 
  return $padSeqWalk;

?>