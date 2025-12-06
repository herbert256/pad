<?php

  include_once PAD . 'bool.php';

  return pqBoolPrime ($pqLoop);
  
  if ( gmp_prob_prime ($pqLoop) )
    return $pqLoop;
  else
    return intval (gmp_nextprime ($pqLoop));

?>