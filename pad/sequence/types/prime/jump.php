<?php

  if ( ! function_exists ( 'gmp_prob_prime' ) )
    return $pqLoop;

  if ( gmp_prob_prime ($pqLoop) )
    $pqJump = $pqLoop;
  else
    $pqJump = intval ( gmp_nextprime ($pqLoop) );

  $pqLoop = $pqJump + $pqInc;
 
  return $pqJump;

?>