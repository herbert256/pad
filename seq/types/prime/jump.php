<?php
  
  if ( gmp_prob_prime ($padSeqLoop) )
    $padSeqJump = $padSeqLoop;
  else
    $padSeqJump = intval ( gmp_nextprime ($padSeqLoop) );

  $padSeqLoop = $padSeqJump + $padSeqInc;
 
  return $padSeqJump;

?>