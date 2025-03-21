<?php

  if ( ! function_exists ( 'gmp_prob_prime' ) )
    return $padSeqLoop;

  if ( gmp_prob_prime ($padSeqLoop) )
    $padSeqJump = $padSeqLoop;
  else
    $padSeqJump = intval ( gmp_nextprime ($padSeqLoop) );

  $padSeqLoop = $padSeqJump + $padSeqInc;
 
  return $padSeqJump;

?>